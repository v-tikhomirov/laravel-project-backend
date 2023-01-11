<?php

namespace App\Http\Controllers;

use App\Enum\UserStatus;
use App\Http\Resources\CompanyFullResource;
use App\Http\Resources\UserResource;
use App\Mail\EmailVerification;
use App\Models\User;
use App\Models\User\Profile;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Laravel\Socialite\Facades\Socialite;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use PhpParser\Node\Expr\Array_;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'registration', 'verifyEmail', 'socialite']]);
    }

    public function socialite($driver) {
        $socUser = Socialite::driver($driver)->stateless()->user();
        $user = User::where('email', $socUser->email)->first();
        if(!$user) {
            $pars = Array();
            $state = request()->input('state');
            parse_str($state, $pars);
            if(!isset($pars['isCompany'])) {
                return response()->json(['error' => 'Unauthorized'], 401);
            } else {
                $data['email'] = $socUser->email;
                switch (intval($pars['isCompany'])) {
                    case 0:
                        $data['type'] = UserStatus::USER;
                        break;
                    case 1:
                        $data['type'] = UserStatus::COMPANY;
                        break;
                    default:
                        return response()->json(['message' => 'User creation error: wrong type']);
                }
                $data['password'] = Hash::make('nopass'. Carbon::now()->timestamp);
                try {
                    $user = User::create($data);
                    if(isset($user->id)) {
                        $data = array();
                        $user->email_verified_at = now();
                        $token = JWTAuth::fromUser($user);
                        $data['user_id'] = $user->id;
                        $data['country_code'] = 0;
                        $data['native_language_id'] = 0;
                        $data['first_name'] = $socUser->first_name ?? '';
                        $data['last_name'] = $socUser->last_name ?? '';
                        Profile::create($data);
                        $user->save();
                        return $this->respondWithToken($token);
                    } else {
                        return response()->json(['message' => 'User is not found']);
                    }
                } catch (\Exception $e) {
                    return response()->json(['message' => 'User creation error']);
                }
            }
        }
        $token = JWTAuth::fromUser($user);
        return $this->respondWithToken($token);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return JsonResponse
     */
    public function login(): JsonResponse
    {
        $credentials = request(['email', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function registration(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(),[
            'email' => 'required|unique:users',
            'password' => 'required',
            'type' => 'required',
        ]);
        if($validator->fails()) {
            return response()->json(['message' => 'Validation error', 'errors' => $validator->errors()]);
        }
        $data = $validator->validated();
        switch ($data['type']) {
            case 0:
                $data['type'] = UserStatus::USER;
                break;
            case 1:
                $data['type'] = UserStatus::COMPANY;
                break;
            default:
                return response()->json(['message' => 'User creation error']);
        }
        $data['password'] = Hash::make($data['password']);
        try {
            $user = User::create($data);
            Mail::to($user->email)->send(new EmailVerification($user->email));
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            Log::error($e->getTraceAsString());
            return response()->json(['message' => 'User creation error']);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function verifyEmail(Request $request): JsonResponse
    {
        if($hash = $request->get('hash')) {
            $email = decrypt($hash);
            if($email) {
                $user = User::where('email', $email)->whereNull('email_verified_at')->first();
                if(isset($user->id)) {
                    $user->email_verified_at = now();
                    $token = auth()->login($user);
                    $data['user_id'] = auth()->user()->id;
                    $data['country_code'] = 0;
                    $data['native_language_id'] = 0;
                    Profile::create($data);
                    $user->save();
                    return $this->respondWithToken($token);
                } else {
                    return response()->json(['message' => 'User is not found']);
                }
            } else {
                return response()->json(['message' => 'Wrong link']);
            }
        } else {
            return response()->json(['message' => 'Wrong link']);
        }
    }

    /**
     * Get the authenticated User.
     *
     * @return JsonResponse
     */
    public function me(): JsonResponse
    {
        $user = auth()->user();
        $user->load('profile');
        if ($user->type == User::TYPE_COMPANY){
            $user->load(['companies']);
        }

        return response()->json(UserResource::make($user)->resolve());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return JsonResponse
     */
    public function refresh(): JsonResponse
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param string $token
     *
     * @return JsonResponse
     */
    protected function respondWithToken(string $token): JsonResponse
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
}
