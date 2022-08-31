<?php
namespace App\Controllers\Data;

use CodeIgniter\Controller;
use App\Models\Workdate;
use App\Models\Datapath;

class Show_data_table extends Controller//連結workdate, datapath資料庫
{
    public function index()
    {
        if(!$this->request->isAJAX()){
            return false;
        }
        if(!isset($_POST['sel_vid'])){
            return redirect()->to("Search");
        }
        $model = model(Workdate::class);
        $data = $model->getlist($_POST['sel_data_type'], $_POST['sel_vid']);
        return json_encode($data);
    }

    public function checkHours(){
        $model = model(Datapath::class);
        $tmp = $_POST['inp_date'][0].$_POST['inp_date'][1].$_POST['inp_date'][2].$_POST['inp_date'][3].$_POST['inp_date'][5].$_POST['inp_date'][6].$_POST['inp_date'][8].$_POST['inp_date'][9];
        $data = $model->where("car_Id", $_POST['sel_vid'])->where("interface", $_POST['sel_data_type'])->where("date", $tmp)->findAll();
        
        return json_encode($data);
    }
}
?>
