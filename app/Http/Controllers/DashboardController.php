<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     * @param AdminRoutes $datatables
     * Inyecta AdminRoutes para acceder a las rutas y datos de esquema
     *
     * @return void
     */
    protected $datatables;
    protected $dataMetrics;

    public function __construct()
    {
        $this->middleware('auth');

    }
    /**
     * Show the application dashboard.
     * 
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function index()
    {
        $title= "Dashboard";
        return view('admin.dashboard', compact('title'));
    }
}
