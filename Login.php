<?php
namespace App\Controllers;

use App\Models\UsersModel;
use CodeIgniter\Controller;

class Login extends Controller{//登入頁面
    public function _remap($method, ...$params)
    {
        $method = ''.$method;
        if (method_exists($this, $method)) {
            return $this->$method(...$params);
        }
        throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
    }
    protected function index()//default
    {
        //session 啟動紀錄帳密
		$session = session();
        //初始化
        session()->stop();

        helper('html');
        //建立變數
        $data=['sub_title'=>'Welcome'];
        //渲染view/畫面
        echo view('templates/header_protected');
        echo view('Login/Login', $data);
    }
    protected function search()//check account and password
    {
        //session 啟動紀錄帳密
        $session =  session();
        //檢查驗證碼
        if(md5($_POST['code']) != $_POST['md5code']){
            $session->setFlashdata("msg", "驗證碼錯誤");
            return redirect()->to('/login');
        }
        //連接使用者資料庫
        $model = model(UsersModel::class);
        //account
        $account = esc( $this->request->getVar("account") );
        //pass
        $password = esc( $this->request->getVar("password") );
        //搜尋資料庫
        $data = $model->where("account", $account)->first();
        
        //成功
        if($data){
            $pass = $data['password'];
            if(password_verify($password."HUE(OHSs07", $pass)){

                if(strtotime($data['deadline']) < time()){
                    $session->setFlashdata("msg", "您的權限已過期");
                    return redirect()->to('/login');
                }


                $user_data=[
                    'account'=>$data['account'],
                    'password'=>$data['password']
                ];
                $session->setFlashdata("msg", "success");
                //session紀錄使用者
                $session->set($user_data);
                //重導向至搜尋頁面
                return redirect()->to('Search');
            }
            else{//密碼錯誤
                $session->setFlashdata("msg", "password error");
                return redirect()->to('/login');
            }
        }
        //帳號不存在
        $session->setFlashdata("msg", "account not found");
        return redirect()->to('/login');

    }
    public function logout()
    {
        $session = session();
        //clear session
        $session->remove('account');
        $session->destroy();
        //重導向至登入畫面
        return redirect()->to('/login');
    }
}