<?php

namespace App\Controllers;

class Home extends BaseController//轉址至login
{
    public function index()
    {
    	return redirect()->to('Login');
    }
}
