<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class GraphicsController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    public function getSchoolIcon($icon_id){
        $path = public_path('schools/'.$icon_id);
        if(!file_exists($path)){
            abort(404);
        }else{
            $file = file_get_contents($path);
            $type = mime_content_type($path);
            return response($file)->header('Content-type', $type);
        }
    }

    public function postSchoolIcon(Request $req){
        $req->validate([
            'image' => 'required|image|mimes:jpeg,png|max:4096',
            'school_id' => 'required'
        ]);
        $school_id = $req->input('school_id');
        if($req->file('image')->isValid()){
            $imgHashName = md5(date('l jS \of F Y h:i:s A'));
            $imgOriginExtension = $req->file('image')->getClientOriginalExtension();
            $imgName = $imgHashName.'.'.$imgOriginExtension;
            Storage::putFileAs('./schools', $req->file('image'), $imgName);
            \DB::update("UPDATE `projekat2_schools` SET img_path = ? WHERE id = ?", [$imgName, $school_id]);
            return json_encode([
                "img_path" => $imgName
            ]); //success
        }
        return json_encode([
            "img_path" => "none"
        ]); //failure
    }

    public function postSchoolIconEncoded(Request $req){
        $req->validate([
            'image' => 'required|image',
            'school_id' => 'required'
        ]);
        $imgFile = $req->file('image');
        $fileName = uniqid().'.'.$imgFile->getClientOriginalExtension();
        Storage::putFileAs('./schools', $imgFile, $fileName);
    }

    public function getStudentIcon(Request $req, $icon_id){
        $icon_id = trim($icon_id, "\n ");
        $files = Storage::files('./students/male');
        foreach($files as $file){
            $pathParts = pathinfo($file);
            $fileName = $pathParts['filename'];
            $extension = $pathParts['extension'];
            $fullFileName = $fileName . '.' . $extension;
            if($fullFileName == $icon_id){
                $retFile = file_get_contents('./students/male/' . $fullFileName);
                $mimeType = mime_content_type('./students/male/' . $fullFileName);
                return response($retFile)->header('Content-type', $mimeType);
            }
        }
        // --------- CHECK FEMALE FOLDER NOW -----------//
        $files = Storage::files('./students/female');
        foreach($files as $file){
            $pathParts = pathinfo($file);
            $fileName = $pathParts['filename'];
            $extension = $pathParts['extension'];
            $fullFileName = $fileName . '.' . $extension;
            if($fullFileName == $icon_id){
                $retFile = file_get_contents('./students/female/' . $fullFileName);
                $mimeType = mime_content_type('./students/female/' . $fullFileName);
                return response($retFile)->header('Content-type', $mimeType);
            }
        }
        return response('not found',404);
    }

    public function insertSchoolMapIcon(Request $req, $id, $posX, $posY, $fullImgPath, $license_id, $next_action_key){
        $verificationResult = json_decode(app(LicenseController::class)->verifyAndGetLicenseNext($license_id, $next_action_key));
        $status = $verificationResult->status;
        if($status == 200){
            $validator = Validator::make([
                'id'          => $id,
                'posX'        => $posX,
                'posY'        => $posY,
                'fullImgPath' => $fullImgPath
            ],[
                'id'          => 'required',
                'posX'        => 'required',
                'posY'        => 'required',
                'fullImgPath' => 'required|max:128'
            ]);
            if(!$validator->fails()){
                $check = \DB::select("SELECT id FROM `projekat2_schools` WHERE id = ?", [$id]);
                if(count($check) == 1){
                    \DB::insert("INSERT INTO `projekat2_school_icons` (school_id, X, Y, img_path) VALUES (?, ?, ?, ?)",[
                                                                                                                $id,
                                                                                                                $posX,
                                                                                                                $posY,
                                                                                                                $fullImgPath
                    ]);
                    return response(json_encode([
                        "status" => "success"
                    ]), 200);
                }
            }
            return response(json_encode([
                "status" => "failed"
            ]), 400);
        }else{
            return response('', $status);
        }
    }

    public function getSchoolIcons(Request $req, $license_id, $next_action_key){
        $verificationResult = json_decode(app(LicenseController::class)->verifyAndGetLicenseNext($license_id, $next_action_key));
        $status = $verificationResult->status;
        if($status == 200){
            $result = \DB::select("SELECT * FROM `projekat2_school_icons`");
            return response()->json($result);
        }else{
            return response('', $status);
        }
    }

    public function getStudentIcons(Request $req, $license_id, $next_action_key){
        $verificationResult = json_decode(app(LicenseController::class)->verifyAndGetLicenseNext($license_id, $next_action_key));
        $status = $verificationResult->status;
        if($status == 200){
            $result = \DB::select("SELECT * FROM `projekat2_student_icons`");
            return response()->json($result);
        }else{
            return response('', $status);
        }
    }

    public function insertStudentMapIcon(Request $req, $jmbg, $posX, $posY, $fullImgPath, $license_id, $next_action_key){
        $verificationResult = json_decode(app(LicenseController::class)->verifyAndGetLicenseNext($license_id, $next_action_key));
        $status = $verificationResult->status;
        if($status == 200){
            $validator = Validator::make([
                'jmbg'          => $jmbg,
                'posX'        => $posX,
                'posY'        => $posY,
                'fullImgPath' => $fullImgPath
            ],[
                'jmbg'          => 'required',
                'posX'        => 'required',
                'posY'        => 'required',
                'fullImgPath' => 'required|max:128'
            ]);
            if(!$validator->fails()){
                $check = \DB::select("SELECT JMBG FROM `projekat2_students` WHERE JMBG = ?", [$jmbg]);
                if(count($check) == 1){
                    \DB::insert("INSERT INTO `projekat2_student_icons` (student_jmbg, X, Y, img_path) VALUES (?, ?, ?, ?)",[
                                                                                                                    $jmbg,
                                                                                                                    $posX,
                                                                                                                    $posY,
                                                                                                                    $fullImgPath
                    ]);
                    return response(json_encode([
                        "status" => "success"
                    ]), 200);
                }
            }else{
                return response('', 400);
            }
        }else{
            return response(json_encode([
                "status" => "failed"
            ]), $status);
        }
    }

    public function removeStudentMapIcon(Request $req, $jmbg, $license_id, $next_action_key){
        $verificationResult = json_decode(app(LicenseController::class)->verifyAndGetLicenseNext($license_id, $next_action_key));
        $status = $verificationResult->status;
        if($status == 200){
            $validator = Validator::make([
                'jmbg' => $jmbg
            ],[
                'jmbg' => 'required|min:3|max:14'
            ]);
            if(!$validator->fails()){
                $check = \DB::select("SELECT student_jmbg FROM `projekat2_student_icons` WHERE student_jmbg = ?", [$jmbg]);
                if(count($check) == 1){
                    \DB::delete("DELETE FROM `projekat2_student_icons` WHERE student_jmbg = ?", [$jmbg]);
                    return json_encode([
                        "status" => "success"
                    ]);
                }else{
                    return response('', 404);
                }
            }else{
                return response('', 400);
            }
        }else{
            return response(json_encode([
                "status" => "failed"
            ]), $status);
        }
    }

    public function removeSchoolMapIcon(Request $req, $id, $license_id, $next_action_key){
        $verificationResult = json_decode(app(LicenseController::class)->verifyAndGetLicenseNext($license_id, $next_action_key));
        $status = $verificationResult->status;
        if($status == 200){
            $validator = Validator::make([
                'id' => $id
            ],[
                'id' => 'required'
            ]);
            if(!$validator->fails()){
                $check = \DB::select("SELECT school_id FROM `projekat2_school_icons` WHERE school_id = ?", [$id]);
                if(count($check) == 1){
                    \DB::delete("DELETE FROM `projekat2_school_icons` WHERE school_id = ?", [$id]);
                    return response(json_encode([
                        "status" => "success"
                    ]), 200);
                }else{
                    return response('', 404);
                }
            }
            return response(json_encode([
                "status" => "failed"
            ]), 400);
        }else{
            return response('', $status);
        }
    }

}
