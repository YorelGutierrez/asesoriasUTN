<?php

namespace App\Http\Controllers;

use App\Models\carreras;
use App\Models\materias;
use App\Models\alumnos;
use App\Models\sesiones_asesoria;
use App\Models\User;
use App\Models\archivos_asesoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use Barryvdh\DomPDF\Facade\Pdf;

class AsesoriaController extends Controller
{
    public function create()
    {
        $carreras = carreras::all();
        $materias = materias::all();
        $alumnos = alumnos::with(['user', 'grupo'])->get();
        
        return view('auth.docentes.registro_asesorias', compact('carreras', 'materias', 'alumnos'));
    }

public function store(Request $request)
{
    try {
        // Preparar datos
        $carrera = carreras::find($request->carrera_id);
        $materia = materias::find($request->materia_id);
        
        $alumnosData = alumnos::with(['user', 'grupo'])->whereIn('id', $request->alumnos)->get();
        
        $data = [
            'carrera_nombre' => $carrera ? $carrera->nombre : 'No especificada',
            'materia_nombre' => $materia ? $materia->nombre : 'No especificada',
            'tipo_asesoria' => $request->tipo_asesoria,
            'tema' => $request->tema,
            'fecha' => $request->fecha,
            'hora_inicio' => $request->hora_inicio,
            'hora_fin' => $request->hora_fin,
            'alumnos' => [],
        ];

        foreach ($alumnosData as $alumno) {
            $data['alumnos'][] = [
                'nombre' => $alumno->user->nombres . ' ' . $alumno->user->apellido_paterno . ' ' . $alumno->user->apellido_materno,
                'grupo' => $alumno->grupo ? $alumno->grupo->nombre : 'N/A',
            ];
        }

        // ========== 1. GENERAR WORD ==========
        try {
            $phpWord = new \PhpOffice\PhpWord\PhpWord();
            $section = $phpWord->addSection();
            
            $section->addTitle('Registro de Asesoría Académica', 1);
            $section->addText('Universidad Tecnológica de Nayarit');
            $section->addTextBreak(1);
            
            $section->addText('INFORMACIÓN GENERAL', ['bold' => true]);
            $section->addText('Carrera: ' . $data['carrera_nombre']);
            $section->addText('Tipo de asesoría: ' . ucfirst($data['tipo_asesoria']));
            $section->addText('Asignatura: ' . $data['materia_nombre']);
            $section->addText('Tema: ' . $data['tema']);
            $section->addText('Fecha: ' . $data['fecha']);
            $section->addText('Horario: ' . ($data['hora_inicio'] ?? '') . ' - ' . ($data['hora_fin'] ?? ''));
            $section->addTextBreak(1);
            
            $section->addText('LISTA DE ALUMNOS', ['bold' => true]);
            
            $table = $section->addTable();
            $table->addRow();
            $table->addCell(500)->addText('#', ['bold' => true]);
            $table->addCell(4000)->addText('Nombre del Alumno', ['bold' => true]);
            $table->addCell(1500)->addText('Grupo', ['bold' => true]);
            $table->addCell(2000)->addText('Firma', ['bold' => true]);
            
            foreach ($data['alumnos'] as $index => $alumno) {
                $table->addRow();
                $table->addCell(500)->addText($index + 1);
                $table->addCell(4000)->addText($alumno['nombre']);
                $table->addCell(1500)->addText($alumno['grupo']);
                $table->addCell(2000)->addText('_________________');
            }
            
            // Guardar Word
            $nombreArchivo = 'asesoria_' . date('Ymd_His') . '.docx';
            $ruta = 'asesorias/' . $nombreArchivo;
            
            $tempFile = tempnam(sys_get_temp_dir(), 'word');
            $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
            $objWriter->save($tempFile);
            
            Storage::disk('public')->put($ruta, file_get_contents($tempFile));
            unlink($tempFile);
            
            // Guardar registro
            archivos_asesoria::create([
                'id_referencia' => 0,
                'tipo_referencia' => 'sesion',
                'nombre_archivo' => $nombreArchivo,
                'ruta' => $ruta,
                'subido_por' => auth()->user()->id,
            ]);
            
        } catch (\Exception $e) {
            // Si falla el Word, continuamos igual con el PDF
            \Log::error('Error al generar Word: ' . $e->getMessage());
        }

        // ========== 2. GENERAR PDF ==========
        $pdf = Pdf::loadView('pdf.asesoria', ['data' => $data]);
        $pdf->setPaper('A4', 'portrait');
        
        return $pdf->download('asesoria_' . date('Ymd_His') . '.pdf');

    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Error: ' . $e->getMessage() . ' | Línea: ' . $e->getLine());
    }
}
    
