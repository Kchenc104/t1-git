<?php
namespace App\Controllers\Data;

use CodeIgniter\Controller;
use App\Models\VelCorr;

class Get_can_type extends Controller//從velcorr model 連結資料庫
{
    public function index()
    {
        if(!isset($_POST['sel_vid'])){
            return redirect()->to("Search");
        }
        $model = model(VelCorr::class);
        $data = $model->getcan($_POST['sel_vid']);
        return json_encode($data);
    }
}
?>