<?php

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\User\AuthController;
use App\Http\Controllers\Api\User\DonorsController;
use App\Http\Controllers\Api\User\GeneralController;
use App\Http\Controllers\Api\User\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });


Route::group(['middleware'=>['api'] , 'namespace'=>'Api'] , function(){
    ######################## statr user authentocation api routes for unauthentecation  #####################
    Route::group(['prefix'=>'user' , 'namespace'=>'User'] , function(){
        Route::post('register', [AuthController::class , 'register'] );
        Route::post('login', [AuthController::class , 'login'] );
        //this route only for authentocation users
        Route::get('logout',[AuthController::class , 'logout']) -> middleware(['auth.guard:user-api']); //تم اضافة الميدلوير لان لازم يكون المستخدم مسجل زخول علشان يعرف يعمل لوجاويت

    });
    ######################## end user authentocation api routes for unauthentecation  ########################

    ################### start general api routes for unauthentecation user #############
    Route::group(['prefix'=>'user' , 'namespace'=>'User'] , function(){
        Route::get('chronic-disease', [GeneralController::class , 'allChronicDiseases'] );
        Route::get('provinces', [GeneralController::class , 'allProvinces'] );
        Route::post('add-donor', [DonorsController::class , 'addDonor'] );
        Route::get('donors', [DonorsController::class , 'getAllDonors'] );
        Route::post('donors-search', [DonorsController::class , 'searchAboutDonorByProvincesAndBloodType'] );
    });
    ################### end general api routes for unauthentecation user ################

    ##################### start profile api routs for authentocated users ###########################
    Route::group(['prefix'=>'user' , 'namespace'=>'User' , 'middleware'=>['auth.guard:user-api']] , function(){
        // هنا مفروض يتحط الروتس اللى لازم يشوفها اليوزر وهو مسجل
        Route::group(['prefix'=>'profile'] , function(){
            Route::post('personal-information' , [ProfileController::class , 'getPersonalInfo']);
            Route::post('profile-picture' , [ProfileController::class , 'addProfilePicture']);
            Route::post('add-chronic-disease' , [ProfileController::class , 'addChronicDisease']);
            Route::post('chronic-disease-data' , [ProfileController::class , 'getUserChronicDisease']);
            Route::post('delete-chronic-disease' , [ProfileController::class , 'deleteDisease']);
            Route::post('add-diagnosis' , [ProfileController::class , 'addDiagnosis']);
            Route::post('edit-diagnosis' , [ProfileController::class , 'editDiagnosis']);
            Route::post('diagnosis-data' , [ProfileController::class , 'getUserDiagnosis']);
        });
    });
    ##################### end profile api routs for authentocated users ###########################
});
