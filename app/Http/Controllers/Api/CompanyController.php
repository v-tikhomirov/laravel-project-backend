<?php

namespace App\Http\Controllers\Api;

use App\Enum\UserStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateCompanyRequest;
use App\Http\Resources\CompanyFullResource;
use App\Http\Resources\CompanyLinksResource;
use App\Mail\EmailVerification;
use App\Mail\Invitation;
use App\Models\Company;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Js;

class CompanyController extends Controller
{
    public function index(): JsonResponse
    {
        $user = auth()->user();
        $company = $user->companies->first();
        $company = Company::find($company->id);
        if ($company) {
            $company->load(['balance', 'benefits']);
            return response()->json(
                getResponseStructure(CompanyFullResource::make($company)->resolve())
            );
        }

        return response()->json(
            getResponseStructure([], false, 'not_found')
        );
    }

    public function getTeam(): JsonResponse
    {
        $user = auth()->user();
        $company = $user->companies->first();
        if ($company) {
            $company->load('users.profile');
            $users = $company->users;

            return response()->json([
                'success' => true,
                'team' => $users
            ]);
        }

        return response()->json([
            'error' => true
        ]);
    }

    public function list(): JsonResponse
    {
        $user = auth()->user();
        return response()->json([
            'companies' => $user->companies
        ]);
    }

    public function removeMember($id) {
        $user = User::find($id);
        if($user) {
            if($user->type === "company" && $user->is_invited === 1) {

                $removerCompany = auth()->user()->companies->first();
                $removable = $user->companies->first();
                if($removerCompany->id === $removable->id) {
                    $user->delete();
                    return response()->json(['success' => true]);
                }
            }
        }
        return response()->json(['success' => false]);
    }

    public function getLinks(): JsonResponse
    {
        $user = auth()->user();
        $company = $user->companies->first();
        return response()->json(
            getResponseStructure(CompanyLinksResource::make($company)->resolve())
        );
    }

    public function create(Request $request): JsonResponse
    {
        $data = $request->all();
        $company = Company::create($data);
        $company->balance()->create(['amount' => 0]);
        auth()->user()->companies()->attach($company->id);

        return response()->json(['success' => true, 'company_id' => $company->id]);
    }

    private function updateLogo($data, $request) {
        if(isset($data['logo'])) {
            if(!is_string($data['logo']))
            {
                $imageName = md5(time());
                $ext = strtolower($data['logo']->getClientOriginalExtension());
                $imageFullName = $imageName . '.' . $ext;
                $uploadPath = 'uploads/images/';

                $imageUrl = $uploadPath . $imageFullName;
                $success = $request->logo->move('media/' . $uploadPath, $imageFullName);

                if ($success) $companyData['logo'] = '/' . $imageUrl;
                else $companyData['logo'] = '';
            } else $companyData['logo'] = $data['logo'];
        }
        else {
            $companyData['logo'] = '';
        }
        return $companyData['logo'];
    }

    public function update(UpdateCompanyRequest $request): JsonResponse
    {
        $data = $request->validated();
        if(isset($data['id'])) $company = Company::find($data['id']);

        if (isset($company)) {
            switch ($data['step']) {
                case 'logo':
                    $companyData['logo'] = $this->updateLogo($data, $request);
                    $company->update($companyData);
                    break;
                case 'about':
                    $type = array_filter($data['type']);
                    $companyData = [
                        'name' => $data['name'],
                        'type' => count($type) > 0 ? implode(';',$type) : '',
                        'website' => $data['website'],
                        'about' => $data['about'],
                        'number_of_employees' => $data['number_of_employees'],
                        'country_id' => $data['location']['country'],
                        'city_id' => $data['location']['city'],
                    ];
                    if(isset($data['logo'])) {
                         $companyData['logo'] = $this->updateLogo($data, $request);
                    }
                    $company->update($companyData);
                    $company->benefits()->sync($data['benefits']);
                    break;
                case 'links':
                    $companyData = [
                        'link_to_linkedin' => $data['linkedin'],
                        'link_to_github' => $data['github'],
                        'link_to_medium' => $data['medium'],
                        'link_to_youtube' => $data['youtube'],
                        'link_to_stackoverflow' => $data['stackoverflow'],
                        'link_to_facebook' => $data['facebook'],
                    ];

                    $company->update($companyData);
                    break;
                case 'companyJourney2':

                    $companyData = $this->processDataForCompanyJourney($data);
                    $company->update($companyData);
                    $company->benefits()->sync($data['benefits']);
                    $company->domains()->sync($data['domains']);
                    break;
            }
            return response()->json(
                getResponseStructure([
                    'id' => $company->id,
                    'company' => $company
                ])
            );
        } else {
            switch ($data['step']) {
                //todo: move this feature to "create" function
                case 'companyJourney2':

                    $companyData = $this->processDataForCompanyJourney($data);

                    $company = Company::create($companyData);
                    $company->balance()->create(['amount' => 0]);
                    auth()->user()->companies()->attach($company->id);
                    $company->benefits()->sync($data['benefits']);
                    $company->domains()->sync($data['domains']);
                    break;
            }
            return response()->json(
                getResponseStructure([
                    'id' => $company->id
                ])
            );
        }
    }
    private function processDataForCompanyJourney(Array $data): array
    {
        $type = array_filter($data['type']);

        $companyData = [
            'name' => $data['name'],
            'type' => count($type) > 0 ? implode(';',$type) : '',
            'website' => $data['website'],
            'about' => $data['about'],
            'number_of_employees' => $data['number_of_employees'],
            'country_id' => $data['country'],
            'city_id' => $data['city'],
            'link_to_linkedin' => $data['linkedin'],
            'link_to_github' => $data['github'],
            'link_to_medium' => $data['medium'],
            'link_to_youtube' => $data['youtube'],
            'link_to_stackoverflow' => $data['stackoverflow'],
            'link_to_facebook' => $data['facebook'],
        ];

        if($data['logoSrc']) {
            $fileArr = explode('/', $data['logoSrc']);
            $fileName = end($fileArr);
            $uploadPath = 'uploads/images/';

            File::copy(public_path() . '/media' . $data['logoSrc'], public_path() . '/media/' . $uploadPath . $fileName);
            $companyData['logo'] = '/' . $uploadPath . $fileName;
        }

        return $companyData;
    }
    public function invite(Request $request): JsonResponse
    {
        $data = $request->validate([
            'emails' => 'required',
            'company_id' => 'required|numeric'
        ]);
        $company = Company::find($data['company_id']);
        if($data['emails'] && $company) {
            foreach ($data['emails'] as $user) {
                $isUserExist = User::where('email', $user)->first();
                if(!$isUserExist) {
                    $userData['password'] = Hash::make('nopass'. Carbon::now()->timestamp);
                    $userData['type'] = UserStatus::COMPANY;
                    $userData['email'] = $user;
                    $userData['is_invited'] = 1;
                    try {
                        $user = User::create($userData);
                        $user->companies()->attach($company->id);
                        Mail::to($user->email)->send(new Invitation($user->email));
                    } catch (\Exception $e) {
                        Log::error($e->getMessage());
                        Log::error($e->getTraceAsString());
                        return response()->json(['message' => 'User creation error']);
                    }
                }
            }
            return response()->json(['success' => true]);
        }
        return response()->json(['message' => 'User creation error']);
    }
}
