<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\users\ChronicDisease;
use App\Models\users\Province;
use Exception;
use App\Traits\GeneralTrait;
use Illuminate\Http\Request;

class GeneralController extends Controller
{
    use GeneralTrait;
    public function allChronicDiseases(){
        try {
            $ChronicDiseases = ChronicDisease::all();
            return $this->returnData('Chronic_Diseases' , $ChronicDiseases);
        } catch (Exception $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }
    }
    public function allProvinces(){
        try {
            $provinces = Province::all();
            return $this->returnData('provinces' , $provinces);
        } catch (Exception $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }
    }
}
