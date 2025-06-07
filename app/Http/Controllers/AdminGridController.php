<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use App\Library\Crud\DataTables; 

class AdminGridController extends Controller
{
    protected $datatables;

    public function __construct()
    {
        // Inicializa como instancia de DataTables, no como resultado de renderGrid()
        $this->datatables = app('datatables'); // O new DataTables() si no está registrado en el contenedor
    }

    /**
     * Muestra la vista de la grilla.
     */
    public function index()
    {
        if (view()->exists('admin.grid')) {
            return view('admin.grid');
        }
        Log::warning('Vista admin.grid no encontrada');
        return response()->json(['error' => 'Vista no encontrada'], 404);
    }

    /**
     * Maneja la solicitud de datos para la grilla.
     */
    public function getdata(Request $request)
    {
        try {
            $tableName = $request->segment(3);
            
            Log::info('Procesando solicitud para tabla: ' . $tableName, [
                'url' => $request->fullUrl(),
                'params' => $request->all(),
            ]);

            // Llama a renderGrid en la instancia de DataTables
            $response = $this->datatables->renderGrid([
                'tableName' => $tableName,
                'params' => $request->all(), // Pasa parámetros GET
            ]);

            return response()->json($response);
        } catch (\Exception $e) {
            Log::error('Error en getdata para tabla ' . $tableName . ': ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'params' => $request->all(),
            ]);
            return response()->json([
                'error' => 'Error en el servidor',
                'message' => $e->getMessage(),
                'data' => [],
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'draw' => (int) $request->input('draw', 1),
            ], 500);
        }
    }
}