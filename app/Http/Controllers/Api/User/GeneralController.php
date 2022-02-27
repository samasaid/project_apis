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
    public function addp(){
        $ps = ['New Valley' , 'Matruh' , 'Red Sea' , 'Giza' , ' South Sinai' , 'North Sinai',
                'Suez' , 'Beheira' , 'Helwan' , 'Sharqia' , 'Dakahlia' ,'Kafr el-Sheikh','Alexandria',
                'Monufia','Minya','Gharbia','Faiyum','Qena','Asyut','Sohag','Ismailia','Beni Suef','Qalyubia',
                'Aswan','Damietta'
             ];
        foreach($ps as $p){
            Province::create([
                'name'=>$p
            ]);
        }
    }
}
