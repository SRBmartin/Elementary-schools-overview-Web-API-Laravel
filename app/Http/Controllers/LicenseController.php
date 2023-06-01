<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LicenseController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    public function verifyLicense(Request $req, $license_id){
        $validator = Validator::make([
            'license_id' => $license_id
        ],[
            'license_id' => 'required|min:10|alpha_num'
        ]);
        if(!$validator->fails()){
            $result = \DB::select("SELECT license_next_action FROM `projekat2_licenses` WHERE license_id = ?", [$license_id]);
            $json_string;
            if(count($result) == 0){
                $json_string = [
                    "status"            => 404,
                    "next_action"       => "none"
                ];
            }else{
                $json_string = [
                    "status"            => 200,
                    "next_action"       => $result[0]->license_next_action
                ];
            }
        }else{
            $json_string = [
                "status"                => 400,
                "next_action"           => "none"
            ];
        }
        return json_encode($json_string);
    }

    public function verifyAndGetLicenseNext($license_id, $next_action){
        $validator = Validator::make([
            'license_id'  => $license_id,
            'next_action' => $next_action
        ],[
            'license_id'  => 'required|min:10|alpha_num',
            'next_action' => 'required|min:10|alpha_num'
        ]);
        if(!$validator->fails()){
            $result = \DB::select("SELECT license_next_action FROM `projekat2_licenses` WHERE license_id = ? AND license_next_action = ?",[
                                                                                                                $license_id,
                                                                                                                $next_action
        ]);
        if(count($result) != 0){
            $newNextAction = md5(date('Y-m-d H:i:s'));
            \DB::update("UPDATE `projekat2_licenses` SET license_next_action = ? WHERE license_id = ?", [$newNextAction, $license_id]);
            return json_encode([
                "status"        => 200,
                "newNextAction" => $newNextAction
            ]);
        }else{
            return json_encode(["status" => 403]); //forbidden
        }
        }else{
            return json_encode(["status" => 400]); //bad request
        }
    }
}
