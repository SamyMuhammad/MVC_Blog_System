<?php

namespace App\Controllers\Admin;

use System\Controller;
//use System\Database;

class LoginController extends Controller {
    
    /**
     * Display login form
     * @return mixed
     */
    public function index(){
        return $this->view->render('admin/users/login');
    }
}
