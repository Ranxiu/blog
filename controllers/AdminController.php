<?php
namespace controllers;

class AdminController {


    public function index(){

        
        view('admin.index');
      
    }
    public function admin(){

        
        view('admin.admin');
      
    }

    public function top()
    {
        view('admin.top');
    }
    public function menu()
    {
        view('admin.menu');
    }
    public function main()
    {
        view('admin.main');

    }
}




?>