<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminFormController extends Controller
{
    public function __Construct(){
        $this->datatables= app('datatables');
    }
    /**
     * Handle the incoming request.
     */
    public function index()
    {
        if (view()->exists('admin.form')) {
            return view('admin.form');
        }
    }
    public function getcreate()
    {
        return $this->datatables->formHtmlCreate(); 
    }
    public function getupdate()
    {
        return $this->datatables->formHtmlUpdate(); 
		
    }
}
