<?php 
namespace App\Controllers;

use CodeIgniter\Controller;
use App\Libraries\data_process;
use App\Libraries\Datasearchfunc;
use App\Models\IdtoSign;
use App\Models\CAN_Module;


class Analyze extends Controller{//分析頁面

    public function __remap($method, ...$params){
        $method = ''.$method;

        if (method_exists($this, $method)) {
            return $this->$method(...$params);
        }
        throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
    }
    public function index()//https://web/analyze/index
    {
        	

        echo "<div style='background:#333;min-width:100vw;'>&emsp;</div>";
        
        //session start
        $session=session();
        //檢查是否來自search的訪問
        if(!isset($_POST['sub_search']))
            return redirect()->to("Search");
        
        //檢查是否存在sel_can_ids
        if(!isset($_POST['sel_can_ids']))
            $_POST['sel_can_ids'] = [];

        //連結資料庫
        $can_model = Model(CAN_Module::class);
        $can_module_result = $can_model->where('can_type', $_POST['sel_data_type'])->findAll();

        
        if(!isset($_POST['can_type']))
            $_POST['can_type'] = []; 

        
        foreach($can_module_result as $a){//檢查模組資料
            if( array_search($a['module_name'], $_POST['can_type'])>-1 ){
                //如果有找到則放入sel_can_ids一起查詢
                array_push($_POST['sel_can_ids'], '0x'.$a['can_module_id'] );
            }
        }


        //去除重複值
        $_POST['sel_can_ids'] = array_unique($_POST['sel_can_ids']);
        

        
        //data arrangement, conn db, set session
        $process = new data_process();
        //cant find data in db
        if($session->getFlashdata("noData")){
            return redirect()->to("Search");
        }

        $count_time = microtime(true);

        //include
        $Dfunc = new Datasearchfunc();
        
        //search file and ID
        list($transID, $IDname, $trans_name) = $Dfunc->Readcsv();
        //cant find ths selected ID
        if(count($IDname) === 0){
            $session->setFlashdata("noData", "未查到所選取的ID");
            return redirect()->to(site_url("search"));
        }
        
        //紀錄搜尋花費時間
        $count_time = round(microtime(true) - $count_time, 2);


        /***********show DBC table***********/
        $model = model(IdtoSign::class);
        $sql = "SELECT * FROM $model->table WHERE ";
        foreach($IDname as $tmp){
            $tmp = strtolower(explode("0x", $tmp)[1]);
            $sql = $sql."IDname ='$tmp' or ";
            
        }

        $sql = substr($sql, 0, -3);
        
        //deb table setting
        $query = $model->db->query($sql);
        $res = $query->getResultArray();
        $tmp = [];
        for($i=0 ; $i<count($res) ; $i++){
            $index = array_search( '0x'.strtolower($res[$i]['IDname']) , $IDname);
            array_push($tmp, '<tr id="'.$res[$i]['Sign'].'" class="table-light">
                <td><u class="pointer" onclick="chgID('.$index.')">'.$IDname[$index].'</u></td>
                <td class="text_left">
                <p><input type="checkbox" class="'.strtolower($res[$i]['IDname'].'_'.$i).'" name="UserSetting[]" id="'.strtolower($res[$i]['IDname']).'$$'.$res[$i]['Sign'].'">
                <label for="'.strtolower($res[$i]['IDname']).'$$'.$res[$i]['Sign'].'">'.$res[$i]['Sign'].'</label>
                </p>
                </td>
                <td class="text_left">'.$res[$i]['vel'].'</td>
                <td class="text_left">'.$res[$i]['content_describe'].'</td>
                <td class="text_left">'.$res[$i]['scale_unit'].'</td>
                <td class="text_left">'.$res[$i]['resolution'].'</td>
                <td class="text_left">'.$res[$i]['offset'].'</td>
            </tr>');
        }
        
        $data['table'] = $tmp;
        /*******end show DBC table *********/

        //為避免5個視角中有某視角缺漏影片，尋找確定存在的影片
        $t_rep = 0;
        if( count($session->get('VID'))==0 ){
            $t_rep=-1;
            $videoStartTime=-1;
        }else{
            //避免影片缺漏，尋找有影片的鏡頭
            for($i=0 ; $i<$session->get('VID') ; $i++)
            {
                if(isset($session->get('VID')[$i][0])){
                    $t_rep=$i;
                    break;
                }
            }

            /*set video starting time*/
            //store strtotime
            $videoStartTime = [];
            //第一筆為第一個CSV起始時間 第二筆開始為影片起始時間
            array_push($videoStartTime, strtotime($session->get('showData')[0]."00") );
            foreach($session->get('VID')[0] as $t){
                $tmp1 = strtotime(explode("-", explode("_",$t) [3] ) [0]."" );
                array_push($videoStartTime, $tmp1);
            }
            /*end set */
        }


        
        
        //輸出資料到view使用
        $data['can_module'] = $can_module_result;
        $data['count_time'] = $count_time;
        $data['videoStartTime'] = $videoStartTime;
        $data['showData'] = $session->get('showData');
        $data['sub_title'] = 'Data Analyze';
        $data['transID']=$transID;
	    $data['IDname']=$IDname;
        $data['trans_name']=$trans_name;
        $data['VID']=$session->get('VID');
        $data['VID_represent'] = $t_rep;
        $data['form_date'] = $session->get("form_date");
        $data['form_tm_start'] = $session->get("form_tm_start");
        

        //印出view
        echo view('templates/header_protected');
        echo view('templates/header',$data);
        echo view("Analyze/analyze");
        echo view('templates/footer');

        //free session
        session()->remove("showData");
        session()->remove("VID");
        session()->remove("form_date");
		session()->remove("sel_vid");
		session()->remove("selCANID");
		session()->remove("selCANID_Dec");
		session()->remove("path");
		session()->remove("tmp_path");
        session()->remove("form_tm_start");

        
    }
    

}