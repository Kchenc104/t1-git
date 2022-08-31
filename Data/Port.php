<?php
/*
0 : OK
1 : input data not exists
2 : input data already exists in DB
3 : input data type error(require json)

5 : DB conn ERROR
6 : update DB fail

11 : ERROR format: table name error
12 : ERROR format: lost data: table

40: update data_path fail: lost path
41: update data_path fail: lost car_id
42: update data_path fail: lost interface
43: update data_path fail: lost time
44: update data_path fail: lost hash_data


50: update work_date fail: lost car_id
51: update work_date fail: lost workdate
52: update work_date fail: lost start_time
53: update work_date fail: lost end_time
54: update work_date fail: lost interface

60: update vel_corr fail: lost car_id
61: update vel_corr fail: lost attr
62: update vel_corr fail: lost type
63: update vel_corr fail: type error

*/
namespace App\Controllers\Data;

use App\Models\Datapath;
use App\Models\Workdate;
use App\Models\VelCorr;
use App\Models\Binpath;

class Port extends \CodeIgniter\Controller {//給server傳送資料到資料庫用
    private $input=null;
    private $returnState=99;
    private $model;
    public function input($to=false)
    {
        $entityBody = file_get_contents('php://input');
        if($entityBody == ""){
            echo "working";
            return;
        }
        //set data
        $this->input=$entityBody;

        //check data type
        $check=$this->isJson();

        if($check)
            $this->input = json_decode($this->input);
        else{
            $this->returnState=3;
            echo "{\"result\" : ".$this->returnState."}";
            return;
        }
        
        //adjust
        $this->checkData();
        if($this->returnState==1){
            echo "{\"result\" : ".$this->returnState."}";
            return;
        }
        

        //check db conn
        $this->checkTb();
        //connDb
        if($this->returnState != 12)
            $this->connToDb();

        echo "{\"result\" : ".$this->returnState."}";
    }
    private function checkTb(){
        if(!isset($this->input->table))
        {
            $this->returnState=12;
            return;
        }else{
            switch($this->input->table){
                case "data_path":
                    $this->model=model(Datapath::class);
                    break;
                case "work_date":
                    $this->model=model(Workdate::class);
                    break;
                case "vel_corr":
                    $this->model=model(VelCorr::class);
                    break;
                case "bin_path":
                    $this->model=model(Binpath::class);
                    break;
                default:
                    $this->returnState=11;
                    break;
            }
        }
    }

    private function isJson() {
        json_decode($this->input);
        return json_last_error() === JSON_ERROR_NONE;
    }

    private function connToDb(){
        $this->returnState = $this->model->port($this->input);
    }

    private function checkData(){
        if(isset($this->input->data))
            $this->input = $this->input->data[0];
        else
            $this->returnState=1;
    }    
}