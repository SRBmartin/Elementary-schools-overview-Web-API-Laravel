<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SchoolController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    public function getSchools(Request $req, $license_id, $next_action_key){
        $verificationResult = json_decode(app(LicenseController::class)->verifyAndGetLicenseNext($license_id, $next_action_key));
        $status = $verificationResult->status;
        if($status == 200){
            $result = \DB::select("SELECT * from `projekat2_schools`");
            return json_encode($result);
        }else{
            return response('', $status);
        }
    }

    public function addSchool($name, $adress, $license_id, $next_action_key){
        try{
            $verificationResult = json_decode(app(LicenseController::class)->verifyAndGetLicenseNext($license_id, $next_action_key));
            $status = $verificationResult->status;
            if($status == 200){
                $validator = Validator::make([
                    'name'   => $name,
                    'adress' => $adress
                ],[
                    'name'   => 'required|min:3|max:64',
                    'adress' => 'required|min:3|max:64'
                ]);
                if(!$validator->fails()){
                    $imgFiles = [];
                    $files = Storage::files('./schools');
                    foreach($files as $file){
                        $pathParts = pathinfo($file);
                        $fileName = $pathParts['filename'];
                        $extension = $pathParts['extension'];
                        $imageFiles[] = $fileName . '.' . $extension;
                    }
                    $img_path = $imageFiles[random_int(1, count($imageFiles))];
                    $returnedId = \DB::table('projekat2_schools')->insertGetId([
                        'name'   => $name,
                        'adress' => $adress,
                        'img_path' => $img_path
                    ]);
                    return json_encode([
                        "newSchoool_id" => $returnedId,
                        "img_path"      => $img_path
                    ]);
                }else{
                    return response('', 400);
                }
            }else{
                return response('', $status);
            }
        }catch(\Exception $ex){
            return response('', 400);
        }
    }

    public function editSchool(Request $req, $school_id, $newSchoolName, $newSchoolAdress, $license_id, $next_action_key){
        $verificationResult = json_decode(app(LicenseController::class)->verifyAndGetLicenseNext($license_id, $next_action_key));
        $status = $verificationResult->status;
        if($status == 200){
            $validator = Validator::make([
                'school_id'       => $school_id,
                'newSchoolName'   => $newSchoolName,
                'newSchoolAdress' => $newSchoolAdress
            ],[
                'school_id'       => 'required',
                'newSchoolName'   => 'required|min:3|max:64',
                'newSchoolAdress' => 'required|min:3|max:64'
            ]);
            if(!$validator->fails()){
                $existIdCheck = \DB::select("SELECT * FROM `projekat2_schools` WHERE id = ?", [$school_id]);
                if(count($existIdCheck) > 0){
                    \DB::update("UPDATE projekat2_schools SET name = ?, adress = ? WHERE id = ?", [$newSchoolName,$newSchoolAdress,$school_id]);
                    return response('', 200);
                }else{
                    return response('', 404);
                }
            }else{
                return response('', 400);
            }
        }else{
            return response('', $status);
        }
    }

    public function deleteSchool(Request $req, $school_id, $license_id, $next_action_key){
        $verificationResult = json_decode(app(LicenseController::class)->verifyAndGetLicenseNext($license_id, $next_action_key));
        $status = $verificationResult->status;
        if($status == 200){
            $validator = Validator::make([
                'school_id' => $school_id
            ],[
                'school_id' => 'required'
            ]);
            if(!$validator->fails()){
                $existIdCheck = \DB::select("SELECT * FROM `projekat2_schools` WHERE id = ?", [$school_id]);
                if(count($existIdCheck) > 0){
                    \DB::update('UPDATE `projekat2_students` SET enrolled_in = 1 WHERE enrolled_in = ?',[$school_id]);
                    \DB::delete("DELETE FROM `projekat2_school_icons` WHERE school_id = ?", [$school_id]);
                    \DB::delete("DELETE FROM `projekat2_schools` WHERE id = ?", [$school_id]);
                    return response('', 200);
                }
            }else{
                return response('', 400);
            }
        }else{
            return response('', $status);
        }
    }
}
