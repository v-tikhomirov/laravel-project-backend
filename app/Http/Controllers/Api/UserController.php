<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateLinksRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UpdateUserProfileRequest;
use App\Http\Requests\WorkingConditionsRequest;
use App\Http\Resources\CompanyFullResource;
use App\Models\AdditionalLanguages;
use App\Models\Language;
use App\Models\User;
use App\Models\User\Profile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Testing\Fluent\Concerns\Has;

class UserController extends Controller
{
    public function load(): JsonResponse
    {
        $user = User::find(auth()->user()->id);
        $user->load(['profile','companies.branches']);

        return response()->json(['success' => true, 'user' => $user]);
    }

    public function updateProfile(UpdateUserProfileRequest $request): JsonResponse
    {
        $data = $request->validated();
        $profile = Profile::where('user_id',auth()->user()->id)->first();
        if ($profile) {
            if(isset($data['password'])) {
                $profile->user->password = Hash::make($data['password']);
                $profile->user->save();
            }
            if(isset($data['language'])) {
                $language_id = Language::getIdByName($data['language']['name']);
                if($language_id) {
                    $profile->native_language_id = $language_id;
                }
            }
            if(isset($data['additionalLanguages'])) {
                AdditionalLanguages::where('user_id', $profile->user_id)->delete();
                foreach ($data['additionalLanguages'] as $additionalLanguage) {
                    $language_id = Language::getIdByName($additionalLanguage['name']);
                    if($language_id) {
                        $profile->additionalLanguages()->create(['language_id' => $language_id, 'level' => $additionalLanguage['level']]);
                    }
                }
            }
            if(isset($data['profile_picture'])) {
                $imageName = md5(time());
                $ext = strtolower($data['profile_picture']->getClientOriginalExtension());
                $imageFullName = $imageName . '.' . $ext;
                $uploadPath = 'uploads/images/';

                $imageUrl = $uploadPath . $imageFullName;
                $success = $request->profile_picture->move('media/' . $uploadPath, $imageFullName);

                if($success) $data['profile_picture'] = '/' . $imageUrl;
                else $data['profile_picture'] = '';
            }
            $profile->update($data);
            return response()->json(
                getResponseStructure($profile)
            );
        } else {
            $data['user_id'] = auth()->user()->id;
            $data['country_code'] = 0;
            $data['native_language_id'] = 0;
            Profile::create($data);
        }
        return response()->json(['success' => true]);
    }

    public function saveSkills(Request $request): JsonResponse
    {
        $data = $request->all();

        $user = auth()->user();
        $skillSet = [];
        foreach ($data as $item) {
            $skillSet[] = [
                'technology_id' => $item['id']
            ];
        }
        if (!empty($skillSet)) {
            $user->skills()->createMany($skillSet);
        }

        return response()->json(['success' => true]);
    }

    public function getWorkingConditions(): JsonResponse
    {
        $user = auth()->user();
        return response()->json([
            'success' => true,
            'conditions' => $user->conditions
        ]);
    }

    public function saveWorkingConditions(WorkingConditionsRequest $request): JsonResponse
    {
        $data = $request->validated();
        $user = auth()->user();
        $workingConditions = $user->conditions;
        if ($workingConditions === null) {
            $data['user_id'] = $user->id;
            $user->conditions()->create($data);

            return response()->json(['success' => true]);
        }

        $workingConditions->update($data);
        return response()->json(['success' => true]);
    }

    public function removeProfilePicture(): JsonResponse
    {
        $profile = auth()->user()->profile;
        $profile->profile_picture = "";
        $profile->save();
        return response()->json(
            getResponseStructure($profile)
        );
    }

    public function updateSecurity(UpdatePasswordRequest $request): JsonResponse
    {
        $data = $request->validated();
        $user = auth()->user();
        if (isset($data['currentPassword']) && $data['currentPassword']) {
            if (Hash::check($data['currentPassword'], $user->password)) {
                $user->password = Hash::make($data['password']);
            } else {
                return response()->json(
                    getResponseStructure([], false, 'password_incorrect')
                );
            }
        }

        if (isset($data['email']) && $data['email']) {
            $user->email = $data['newEmail'];
        }

        $user->save();

        return response()->json(
            getResponseStructure([])
        );
    }

    public function updateLinks(UpdateLinksRequest $request): JsonResponse
    {
        $data = $request->validated();
        $user = auth()->user();
        $profile = $user->profile;
        if ($profile) {
            $profile->update($data);

            return response()->json([
                'success' => true
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Profile missed'
        ]);
    }

    public function notifications(): JsonResponse
    {
        $user = auth()->user();
        return response()->json([
            'notifications' => $user->notifications
        ]);
    }
}
