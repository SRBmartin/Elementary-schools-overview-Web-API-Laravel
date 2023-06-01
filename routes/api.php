<?php

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

/*
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
*/
//Route::post('/addIcon','App\Http\Controllers\GraphicsController@postSchoolIcon');
//Route::post('/addIcon', 'App\Http\Controllers\GraphicsController@postSchoolIconEncoded');
Route::get('/students/getIcon/{icon_id}', 'App\Http\Controllers\GraphicsController@getStudentIcon');
Route::post('/schools/add/{name}/{adress}/{license_id}/{next_action_key}','App\Http\Controllers\SchoolController@addSchool');
Route::post('/schools/delete/{school_id}/{license_id}/{next_action_key}', 'App\Http\Controllers\SchoolController@deleteSchool');
Route::get('/schools/{license_id}/{next_action_key}', 'App\Http\Controllers\SchoolController@getSchools');
Route::post('/students/add/{jmbg}/{name}/{surname}/{adress}/{gender}/{license_id}/{next_action_key}','App\Http\Controllers\StudentController@addStudent');
Route::post('/students/editStudent/{jmbg}/{name}/{surname}/{adress}/{license_id}/{next_action_key}','App\Http\Controllers\StudentController@editStudent');
Route::post('/students/deleteStudent/{jmbg}/{license_id}/{next_action_key}', 'App\Http\Controllers\StudentController@deleteStudent');
Route::post('/students/changeSchool/{jmbg}/{newSchoolId}/{license_id}/{next_action_key}', 'App\Http\Controllers\StudentController@changeSchool');
Route::get('/students/{license_id}/{next_action_key}', 'App\Http\Controllers\StudentController@getStudents');
Route::get('/schools/{icon_id}', 'App\Http\Controllers\GraphicsController@getSchoolIcon');
Route::get('/map/getSchoolIcons/{license_id}/{next_action_key}', 'App\Http\Controllers\GraphicsController@getSchoolIcons');
Route::get('/map/getStudentIcons/{license_id}/{next_action_key}', 'App\Http\Controllers\GraphicsController@getStudentIcons');
Route::post('/schools/edit/{school_id}/{newSchoolName}/{newSchoolAdress}/{license_id}/{next_action_key}','App\Http\Controllers\SchoolController@editSchool');
Route::post('/verifyLicense/{license_id}', 'App\Http\Controllers\LicenseController@verifyLicense');
Route::post('/map/addSchool/{id}/{posX}/{posY}/{fullImgPath}/{license_id}/{next_action_key}', 'App\Http\Controllers\GraphicsController@insertSchoolMapIcon');
Route::post('/map/addStudent/{jmbg}/{posX}/{posY}/{fullImgPath}/{license_id}/{next_action_key}', 'App\Http\Controllers\GraphicsController@insertStudentMapIcon');
Route::post('/map/removeStudent/{jmbg}/{license_id}/{license_next_action}','App\Http\Controllers\GraphicsController@removeStudentMapIcon');
Route::post('/map/removeSchool/{id}/{license_id}/{license_next_action}','App\Http\Controllers\GraphicsController@removeSchoolMapIcon');