<?php
namespace App\Controllers\Data;

use CodeIgniter\Controller;
use App\Models\CAN_Module;

class Get_search_module extends Controller//從CAN_module model 連結資料庫
{
    public function get_module()
    {
        if(!isset($_POST['module_name']))
            return false;
        $model = model(CAN_Module::class);
        
        $sql = "SELECT DISTINCT module_name FROM $model->table";
        $res = [];
        $query = $model->db->query($sql);
        foreach($query->getResult() as $value){
            array_push($res, $value);
        }
        
        return json_encode($res);
    }
}
?>