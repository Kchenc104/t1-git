<?php
namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\CarName;
use App\Models\IdtoSign;
use App\Models\CAN_Module;

class Search extends Controller{
	public function _remap($method, ...$params)
    {        $method = ''.$method;

        if (method_exists($this, $method)) {
            return $this->$method(...$params);
        }
        throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
    }
    protected function index()//搜尋頁面
    {
		$session=session();
		//noData代表發送搜尋請求後，如果無法抓到檔案，或無法抓到ID時自動返回
		if($session->get("noData")!== null ){
			$data['noData'] = $session->getFlashdata("noData");
		}
		
		//連接資料庫
		$modelCar = model(CarName::class);
		$modelsign = model(IdtoSign::class);
		$can_module = model(CAN_Module::class);

		//查詢資料
		$id_sign = $modelsign->findAll();
		$car_name = $modelCar->findAll();
		$cm = $can_module->findAll();

		//SQL語法
		$sql = "SELECT * FROM $can_module->table group by module_name";
		$query = $can_module->db->query($sql)->getResultArray();
		
		//建立變數
		$data['id_sign'] = $id_sign;
		$data['sub_title'] = "Welcome";
		$data['car_name'] = $car_name;
		$data['can_module'] = $cm;
		$data['can_module_type'] = $query;

		
		//印出view
        echo view('templates/header_protected');
		//印出view並傳送data資料供view使用
		echo view("templates/header", $data);
        echo view("Search/Search");
		echo view("templates/footer");
    }
    

}


