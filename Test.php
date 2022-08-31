<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class Test extends Controller
{
    public function index()
    {
        echo @$_GET['test'];

        echo view("test");
    }
}