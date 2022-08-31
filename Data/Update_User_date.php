<?php
namespace App\Controllers\Data;

use CodeIgniter\Controller;
use App\Models\UsersModel;

class Update_User_date extends Controller//以UsersModel連結資料庫
{
    public function ModUserDate()
    {
        if(!$this->request->isAJAX()){
            return false;
        }
        if(!isset($_SESSION['account']) || !isset($_SESSION['password'])){
            return "please login first";
        }
        $model = model(UsersModel::class);
        $sql = "UPDATE $model->table SET deadline='".esc($_POST['date'])."' WHERE account='".$_POST['account']."'";
        $query = $model->db->query($sql);
        if($query)
            return true;
        else
            return false;
    }

    public function DelUser(){
        if(!$this->request->isAJAX()){
            return false;
        }
        if(!isset($_SESSION['account']) || !isset($_SESSION['password'])){
            return "please login first";
        }
        $model = model(UsersModel::class);
        $sql = "DELETE FROM $model->table WHERE account = '".$_POST['account']."'";
        $query = $model->db->query($sql);
        if($query)
            return true;
        else
            return false;
    }
}
?>
