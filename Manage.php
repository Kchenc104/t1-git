<?php
namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\UsersModel;

class Manage extends Controller//管理使用者頁面
{
    public function manage_user()
    {
        //返回連結
        $tmp = " <a href='/Search'>返回</a>";

        //檢查是否已登入
        if(!isset($_SESSION['account']) || !isset($_SESSION['password'])){
            die("Sorry!!Access failed. please login first!!");
        }


        if(isset($_SESSION['account']) && isset($_SESSION['password']) ){
            //exits account
            
            //連結model
            $model = model(UsersModel::class);
            
            //搜尋資料庫
            $user = $model->where("account", $_SESSION['account'])->first();
            
            //檢查密碼是否正確
            if($_SESSION['password'] == $user['password']){//check password

                //檢查是否有權限
                if($user['level'] != 3)
                    die("Sorry!!Access failed. You don't have permission!!".$tmp);
                else{
                    //檢查是否已過期
                    if(strtotime($user['deadline']) < time())
                        die("Sorry!!Your permission has expired".$tmp);
                    else{
                        
                        $data['user'] = $model->findAll();
                        echo view('templates/header_protected');
                        echo view("templates/header", ["sub_title"=>"Manage User"]);
                        echo view('Manage/manage_user',$data);
                        echo view("templates/footer");
                    }
                }

                
            }else{
                die("Sorry!!Access failed. please login first!!".$tmp);
            }
        }
    }
    //新增使用者
    public function add(){
        //建立保護機制避免惡意輸入，使用esc跳脫函數
        $account = esc($_POST['add_acc']);
        $password = esc(password_hash($_POST['add_pass']."HUE(OHSs07",PASSWORD_DEFAULT));
        $date = esc($_POST['add_date']);
        $level = esc($_POST['add_level']);

        //檢查是否故意
        if($level<0 || $level>3)
            $level = 0;

        //連結UsersModel
        $model = model(UsersModel::class);

        //sql語法
        $sql = "SELECT account FROM $model->table WHERE account = '$account'";

        //送出查詢
        $query = $model->db->query($sql);

        //檢查是否已存在帳號
        if( count( $query->getResultArray() ) ){
            $session = session();
            $session->setFlashdata("msg", "此帳號已存在");
            return redirect()->to('/manage/manage_user');
        }else{


            $sql = "INSERT INTO user(account, password, level, deadline) VALUES ('$account','$password','$level','$date')";
            $query1 = $model->db->query($sql);

            //檢查新增成功並刷新頁面
            if($query1){
                $session = session();
                $session->setFlashdata("msg", "新增成功");
                return redirect()->to('/manage/manage_user');
            }else{//檢查新增失敗並刷新頁面
                $session = session();
                $session->setFlashdata("msg", "新增失敗");
                return redirect()->to('/manage/manage_user');
            }
        }
    }
}
?>