<?php
namespace App\Controllers\Data;

use CodeIgniter\Controller;
use App\Models\CanType;

class Get_can_list extends Controller//從can type model連結資料庫
{
    public function input()
    {
        if(!isset($_POST['sel_vid'])){
            return redirect()->to("Search");
        }
        $model = model(CanType::class);
        if($_POST['sel_data_type'] == "ChassisCan" || $_POST['sel_data_type'] == "CAN2")
            $type = 'ChassisCan';
        else if($_POST['sel_data_type'] == "SensingCan" || $_POST['sel_data_type'] == "CAN1")
            $type = 'SensingCan';
        else if($_POST['sel_data_type'] == "MainCan"){
            $type = 'MainCan';
        }
        $query = $model->db->query("SELECT * FROM $model->table WHERE vel = '".$_POST['sel_vid']."' and interface = '".$type."' ORDER BY classification ");
        return json_encode($query->getResultArray());
        
                 
                    
        
            
    }
}
?>