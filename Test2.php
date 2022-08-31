<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class Test2 extends Controller
{
    public function index()
    {
        echo "account: ".@$_GET['account']."<br>";
        echo "account: ".@$_POST['account'];
        echo "<br>";
        print_r("hello world");
        print_r("<br>");
        $array = [1,2,3,4,5];

        print_r($array);
        echo "<br>";

        echo view("test2");
        

        
    }
    public function input()
    {
        

        echo view("test");
        

        
    }
}