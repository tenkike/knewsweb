<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Dompdf\Dompdf;
use App\Library\Crud\DataTables;
use DB;

class PdfController extends Controller
{
    public function getTableAsPdf(DataTables $dataTables)
    {
        
        // Obtener los datos de la tabla de la base de datos o de cualquier otra fuente
        // Crear el contenido HTML de la tabla
        $html =  $dataTables->gridHtml['table']; 
        //dd($html);
        // Crear una nueva instancia de Dompdf y generar el archivo PDF
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $pdf = $dompdf->output();

        // Devolver el archivo PDF al navegador para que se descargue
        return response($pdf)->header('Content-Type', 'application/pdf')->header('Content-Disposition', 'attachment; filename="table.pdf"');
    }
}
