<?php

use App\Http\Controllers\Api\BalanceController;
use App\Http\Controllers\Api\CommonController;
use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\ComponentController;
use App\Http\Controllers\Api\CvController;
use App\Http\Controllers\Api\MatchController;
use App\Http\Controllers\Api\SkillSetController;
use App\Http\Controllers\Api\TechnologyController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\VacancyController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ImageController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\StripeController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::group(['prefix' => 'component'], function () {
    Route::get('/technology', [ComponentController::class, 'technology']);
    Route::post('/find-cards', [ComponentController::class, 'loadCards']);
});

Route::group([
    'prefix' => 'auth'
], function ($router) {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('registration', [AuthController::class, 'registration']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::get('user', [AuthController::class, 'me']);
    Route::post('verifyEmail', [AuthController::class, 'verifyEmail']);
    Route::get('socialite/{driver}', [AuthController::class, 'socialite']);
});

Route::group(['prefix' => 'v1', 'middleware' => 'api'], function() {
    Route::get('/countries', [CommonController::class, 'countries']);
    Route::get('/country/{id}/cities', [CommonController::class, 'cities']);

    Route::get('/languages', [CommonController::class, 'languages']);

    Route::get('/domains', [CommonController::class, 'domains']);
    Route::get('/benefits', [CommonController::class, 'benefits']);

    Route::group(['prefix' => 'user'], function() {
        Route::get('/',[UserController::class, 'load']);
        Route::get('/notifications', [UserController::class, 'notifications']);
        Route::post('/profile', [UserController::class, 'updateProfile']);
        Route::post('/skills', [UserController::class, 'saveSkills']);
        Route::post('/working-conditions', [UserController::class, 'saveWorkingConditions']);
        Route::get('/working-conditions', [UserController::class, 'getWorkingConditions']);
        Route::post('/update-security', [UserController::class, 'updateSecurity']);
        Route::post('/update-links', [UserController::class, 'updateLinks']);
        Route::post('/remove-profile-picture', [UserController::class, 'removeProfilePicture']);
    });

    Route::group(['prefix' => 'technologies'], function() {
        Route::get('/all', [TechnologyController::class,'all']);
        Route::get('/all/light', [TechnologyController::class,'allLight']);
        Route::get('/group/{group}', [TechnologyController::class, 'getByGroup']);
        Route::get('/root', [TechnologyController::class, 'getRoot']);
        Route::get('/languages', [TechnologyController::class, 'getLanguages']);
    });

    Route::group(['prefix' => 'skills'], function() {
        Route::get('/all', [SkillSetController::class, 'all']);
        Route::post('/save', [SkillSetController::class, 'save']);
    });

    Route::group(['prefix' => 'cv'], function() {
        Route::get('/get', [CvController::class, 'get']);
        Route::get('/list', [CvController::class, 'index']);
        Route::post('/create', [CvController::class, 'create']);
        Route::post('/draft', [CvController::class, 'draft']);
        Route::post('/update', [CvController::class, 'update']);
        Route::get('/load/{slug}', [CvController::class, 'getBySlug']);
    });

    Route::group(['prefix' => 'vacancy'], function() {
        Route::get('/list', [VacancyController::class, 'index']);
        Route::get('/load/{slug}', [VacancyController::class, 'load']);
        Route::post('/create', [VacancyController::class, 'create']);
        Route::post('/create/skills', [VacancyController::class, 'createSkills']);
        Route::post('/update', [VacancyController::class, 'update']);
        Route::post('/archive', [VacancyController::class, 'archive']);
    });

    Route::group(['prefix' => 'company'], function() {
        Route::get('/',[CompanyController::class, 'index']);
        Route::get('/team', [CompanyController::class, 'getTeam']);
        Route::get('/links', [CompanyController::class, 'getLinks']);
        Route::post('create',[CompanyController::class, 'create']);
        Route::post('update', [CompanyController::class, 'update']);
        Route::get('list',[CompanyController::class, 'list']);
        Route::get('init', [CompanyController::class,'index']);
        Route::post('/invite', [CompanyController::class, 'invite']);
        Route::get('/removeMember/{id}', [CompanyController::class, 'removeMember']);
    });

    Route::group(['prefix' => 'matches'], function() {
       Route::get('list', [MatchController::class, 'index']);
       Route::get('archive', [MatchController::class, 'archiveList']);
       Route::get('show/{id}', [MatchController::class, 'show']);
       Route::post('change/status', [MatchController::class, 'changeStatus']);
       Route::post('interview/submit', [MatchController::class, 'submitInterview']);
       Route::post('offer', [MatchController::class, 'offer']);
       Route::post('accept', [MatchController::class, 'accept']);
       Route::post('/decline', [MatchController::class, 'decline']);
       Route::post('/addNote', [MatchController::class, 'addNote']);
       Route::get('/editNote/{id}/{type}', [MatchController::class, 'editNote']);
    });

    Route::group(['prefix' => 'payment'], function() {
        Route::post('connect/open', [BalanceController::class, 'openConnect']);
        Route::get('stripe/createSession/{contactsAmount}/{cvId}', [StripeController::class, 'createSession']);
        Route::get('checkCompanyPayments', [StripeController::class, 'checkCompanyPayments']);
    });

    Route::group(['prefix' => 'image'], function() {
       Route::post('upload', [ImageController::class, 'uploadImage']);
    });
});
