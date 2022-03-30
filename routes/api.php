<?php

use App\Http\Controllers\Api\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Api\Admin\ProfileController as AdminProfileController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\User\AuthController;
use App\Http\Controllers\Api\User\ContactController;
use App\Http\Controllers\Api\User\DonorsController;
use App\Http\Controllers\Api\User\EmailController;
use App\Http\Controllers\Api\User\GeneralController;
use App\Http\Controllers\Api\User\ProfileController;
use App\Http\Middleware\UserActivity;
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

//user apis
Route::group(['middleware'=>['api'] , 'namespace'=>'Api'] , function(){
    ######################## statr user authentocation api routes for unauthentecation  #####################
    Route::group(['prefix'=>'user' , 'namespace'=>'User'] , function(){
        Route::post('register', [AuthController::class , 'register'] );
        Route::post('login', [AuthController::class , 'login'] );/*->middleware(['UserActivity']);*/
        //this route only for authentocation users
        Route::get('logout',[AuthController::class , 'logout']) -> middleware(['auth.guard:user-api' , 'UserActivity']); //تم اضافة الميدلوير لان لازم يكون المستخدم مسجل زخول علشان يعرف يعمل لوجاويت

    });
    ######################## end user authentocation api routes for unauthentecation  ########################

    ################### start general api routes for unauthentecation user #############
    Route::group(['prefix'=>'user' , 'namespace'=>'User'] , function(){
        Route::get('chronic-disease', [GeneralController::class , 'allChronicDiseases'] );
        Route::get('provinces', [GeneralController::class , 'allProvinces'] );
        Route::post('add-donor', [DonorsController::class , 'addDonor'] );
        Route::get('donors', [DonorsController::class , 'getAllDonors'] );
        Route::post('donors-search', [DonorsController::class , 'searchAboutDonorByProvincesAndBloodType'] );
        Route::post('contact-us', [ContactController::class , 'storeContactForm'] );
        Route::post('admin-contact', [EmailController::class , 'sendEmail'] );
        // Route::get('pro', [GeneralController::class , 'addp'] );

    });
    ################### end general api routes for unauthentecation user ################

    ##################### start profile api routs for authentocated users ###########################
    Route::group(['prefix'=>'user' , 'namespace'=>'User' , 'middleware'=>['auth.guard:user-api']] , function(){
        // هنا مفروض يتحط الروتس اللى لازم يشوفها اليوزر وهو مسجل
        Route::group(['prefix'=>'profile' , 'middleware'=>['UserActivity']] , function(){
            Route::post('personal-information' , [ProfileController::class , 'getPersonalInfo']);
            Route::post('edit-information' , [ProfileController::class , 'editUserinfo']);
            Route::post('profile-picture' , [ProfileController::class , 'addProfilePicture']);
            Route::post('add-chronic-disease' , [ProfileController::class , 'addChronicDisease']);
            Route::post('chronic-disease-data' , [ProfileController::class , 'getUserChronicDisease']);
            // Route::post('delete-chronic-disease' , [ProfileController::class , 'deleteDisease']);
            Route::post('chronic-disease-status' , [ProfileController::class , 'changeDiseaseStatus']);
            Route::post('add-diagnosis' , [ProfileController::class , 'addDiagnosis']);
            Route::post('edit-diagnosis' , [ProfileController::class , 'editDiagnosis']);
            Route::post('diagnosis-data' , [ProfileController::class , 'getUserDiagnosis']);
        });
    });
    ##################### end profile api routs for authentocated users ###########################
});
//admin apis
Route::group(['middleware'=>['api'] , 'namespace'=>'Api'] , function(){
    ######################## statr admin authentocation api routes for unauthentecation  #####################
    Route::group(['prefix'=>'admin' , 'namespace'=>'Admin'] , function(){
        Route::post('login', [AdminAuthController::class , 'login'] );
        //this route only for authentocation admin
        Route::get('logout',[AdminAuthController::class , 'logout']) -> middleware(['auth.guard:admin-api']); //تم اضافة الميدلوير لان لازم يكون المستخدم مسجل زخول علشان يعرف يعمل لوجاويت

    });
    ######################## end admin authentocation api routes for unauthentecation  ########################
    ##################### start profile api routs for authentocated admin ###########################
    Route::group(['prefix'=>'admin' , 'namespace'=>'Admin' , 'middleware'=>['auth.guard:admin-api']] , function(){
        // هنا مفروض يتحط الروتس اللى لازم يشوفها اليوزر وهو مسجل
        Route::group(['prefix'=>'profile'] , function(){
            Route::get('admin-info' , [AdminProfileController::class , 'getAdminInfo']);
            Route::post('edit-admin-info' , [AdminProfileController::class , 'editAdminInfo']);
            Route::post('edit-admin-photo' , [AdminProfileController::class , 'addProfilePicture']);
            Route::post('add-advice' , [AdminProfileController::class , 'addAdvice']);
            Route::post('edit-advice' , [AdminProfileController::class , 'editAdvice']);
            Route::post('delete-advice' , [AdminProfileController::class , 'deleteAdvice']);
            Route::post('change-advice-status' , [AdminProfileController::class , 'adviceStatus']);
            Route::post('add-chronic-disease' , [AdminProfileController::class , 'addChronicDisease']);
            Route::post('edit-chronic-disease' , [AdminProfileController::class , 'editChronicDisease']);
            Route::post('delete-chronic-disease' , [AdminProfileController::class , 'deleteChronicDisease']);
            Route::post('delete-user' , [AdminProfileController::class , 'deleteUser']);
            Route::post('delete-donor' , [AdminProfileController::class , 'deleteDonor']);
            Route::post('users-search' , [AdminProfileController::class , 'userSearch']);
            Route::post('edit-user-by-admin' , [AdminProfileController::class , 'editUserId']);
            Route::get('all-advices' , [AdminProfileController::class , 'getAllAdvices']);
            Route::get('all-users' , [AdminProfileController::class , 'getAllUsers']);
            Route::get('counts' , [AdminProfileController::class , 'getCount']);
            Route::get('number-of-user-per-month' , [AdminProfileController::class , 'getNumOfUserForChart']);
            Route::get('number-of-donor-per-month' , [AdminProfileController::class , 'getNumOfDonorForChart']);


        });
    });
    ##################### end profile api routs for authentocated admin ###########################
});
