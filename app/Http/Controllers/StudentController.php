<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class StudentController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    public function getStudents(Request $req, $license_id, $next_action_key){
        $verificationResult = json_decode(app(LicenseController::class)->verifyAndGetLicenseNext($license_id,$next_action_key));
        $status = $verificationResult->status;
        if($status == 200){
            $result = \DB::select("SELECT * from `projekat2_students`;");
            return json_encode($result);
        }else{
            return response('', $status);
        }
    }

    public function addStudent(Request $req, $jmbg, $name, $surname, $adress, $gender, $license_id, $next_action_key){
        $verificationResult = json_decode(app(LicenseController::class)->verifyAndGetLicenseNext($license_id, $next_action_key));
        $status = $verificationResult->status;
        if($status == 200){
            $validator = Validator::make([
                'jmbg'    => $jmbg,
                'name'    => $name,
                'surname' => $surname,
                'adress'  => $adress,
                'gender'  => $gender
            ],
            [
                'jmbg'    => 'required|min:3|max:14',
                'name'    => 'required|min:3|max:32',
                'surname' => 'required|min:3|max:32',
                'adress'  => 'required|min:3|max:64',
                'gender'  => 'required|min:4|max:6'
            ]);
            if(!$validator->fails()){
                $check = \DB::select("SELECT JMBG FROM `projekat2_students` WHERE JMBG = ?", [$jmbg]);
                if(count($check) != 0){
                    return response(json_encode([
                        "status"   => "id exists",
                        "img_path" => "none"
                    ]), 403);
                }else{
                    $path = "";
                    if($gender == 'male'){
                        $path = './students/male';
                    }else{
                        $path = './students/female';
                    }
                    $imgFiles = [];
                    $files = Storage::files($path);
                    foreach($files as $file){
                        $pathParts = pathinfo($file);
                        $fileName = $pathParts['filename'];
                        $extension = $pathParts['extension'];
                        $imageFiles[] = $fileName . '.' . $extension;
                    }
                    $img_path = $imageFiles[random_int(0, count($imageFiles))];
                    \DB::insert("INSERT INTO `projekat2_students` (JMBG,name,surname,adress,img_path) VALUES (?, ?, ?, ?, ?)",[
                                                                                                            $jmbg,
                                                                                                            $name,
                                                                                                            $surname,
                                                                                                            $adress,
                                                                                                            $img_path                                                                              
                    ]);
                    return response(json_encode([
                        "status"   => "success",
                        "img_path" => $img_path
                    ]), 200);
                }
            }
        }
        return response(json_encode([
            "status"   => "failure",
            "img_path" => "none"
        ]), 400);
    }

    public function editStudent(Request $req, $jmbg, $name, $surname, $adress, $license_id, $next_action_key){
        $verificationResult = json_decode(app(LicenseController::class)->verifyAndGetLicenseNext($license_id, $next_action_key));
        $status = $verificationResult->status;
        if($status == 200){
            $validator = Validator::make([
                'jmbg'    => $jmbg,
                'name'    => $name,
                'surname' => $surname,
                'adress'  => $adress
            ],[
                'jmbg'    => 'required|min:3|max:14',
                'name'    => 'required|min:3|max:32',
                'surname' => 'required|min:3|max:32',
                'adress'  => 'required|min:3|max:64'
            ]);
            if(!$validator->fails()){
                $check = \DB::select("SELECT JMBG FROM `projekat2_students` WHERE JMBG = ?", [$jmbg]);
                if(count($check) != 0){
                    \DB::update("UPDATE `projekat2_students` SET name = ?, surname = ?, adress = ? WHERE JMBG = ?",[
                                                                                                        $name,
                                                                                                        $surname,
                                                                                                        $adress,
                                                                                                        $jmbg
                    ]);
                    return response(json_encode([
                        "status" => "success"
                    ]),200);
                }else{
                    return response(json_encode([
                        "status" => "student not found"
                    ]),404);
                }
            }else{
                return response(json_encode([
                    "status" => "failure"
                ]),400);
            }
        }else{
            return response('',$status);
        }
    }

    public function deleteStudent(Request $req, $jmbg, $license_id, $next_action_key){
        $verificationResult = json_decode(app(LicenseController::class)->verifyAndGetLicenseNext($license_id, $next_action_key));
        $status = $verificationResult->status;
        if($status == 200){
            $validator = Validator::make([
                'jmbg' => $jmbg
            ],[
                'jmbg' => 'required|min:3|max:14'
            ]);
            if(!$validator->fails()){
                $check = \DB::select("SELECT JMBG FROM `projekat2_students` WHERE JMBG = ?", [$jmbg]);
                if(count($check) != 0){
                    \DB::delete("DELETE FROM `projekat2_student_icons` WHERE student_jmbg = ?", [$jmbg]);
                    \DB::delete("DELETE FROM `projekat2_students` WHERE JMBG = ?", [$jmbg]);
                    return response(json_encode([
                        "status" => "success"
                    ]), 200);
                }else{
                    return response(json_encode([
                        "status" => "student not found"
                    ]), 404);
                }
            }else{
                return response(json_encode([
                    "status" => "failed"
                ]), 400);
            }
        }
    }

    public function changeSchool(Request $req, $jmbg, $newSchoolId, $license_id, $next_action_key){
        $verificationResult = json_decode(app(LicenseController::class)->verifyAndGetLicenseNext($license_id, $next_action_key));
        $status = $verificationResult->status;
        if($status == 200){
            $validator = Validator::make([
                'jmbg'        => $jmbg,
                'newSchoolId' => $newSchoolId
            ],[
                'jmbg'        => 'required',
                'newSchoolId' => 'required'
            ]);
            if(!$validator->fails()){
                $check = \DB::select("SELECT JMBG FROM `projekat2_students` WHERE JMBG = ?", [$jmbg]);
                if(count($check) == 0){
                    return response(json_encode([
                        "status" => "student not found"
                    ]), 404);
                }else{
                    $check = \DB::select("SELECT id FROM `projekat2_schools` WHERE id = ?", [$newSchoolId]);
                    if(count($check) == 0){
                        return response(json_encode([
                            "status" => "school not found"
                        ]), 404);
                    }else{
                        \DB::update("UPDATE `projekat2_students` SET enrolled_in = ? WHERE JMBG = ?", [$newSchoolId, $jmbg]);
                        return response(json_encode([
                            "status" => "success"
                        ]), 200);
                    }
                }
            }else{
                return response(json_encode([
                    "status" => "failed"
                ]), 400);
            }
        }else{
            return response('', $status);
        }
    }

}
