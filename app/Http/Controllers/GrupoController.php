<?php

namespace App\Http\Controllers;

use App\Models\grupos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GrupoController extends Controller
{
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $grupo = grupos::findOrFail($id);
            $grupo->delete();

            DB::commit();

            return redirect()->route('gestion', ['tab' => 'grupos'])
                ->with('success', 'Grupo eliminado correctamente');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('gestion', ['tab' => 'grupos'])
                ->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
