<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->datatables = app('adminroutes'); // O new DataTables() si no está registrado en el contenedor

    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $title= "Dashboard";
        $getShemaData= $this->datatables::$schemaDataTable;
        
        if (empty($getShemaData)) {
            // Opcional: manejar el caso de datos vacíos
            Log::warning('No se encontraron datos de esquema para el dashboard: ' . json_encode($getShemaData) . ' en ' . __METHOD__);
            $getShemaData = '{}';
        }

        // Compartir las rutas con la vista
        return view('admin.dashboard', compact('title', 'getShemaData'));
    }
}
