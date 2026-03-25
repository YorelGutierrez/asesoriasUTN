<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LocalizationController extends Controller
{
    /**
     * Establece el idioma preferido en la sesión del usuario.
     */
    public function setLang($locale)
    {
        // 1. Valida que el idioma que nos piden ('en' o 'es') esté permitido
        if (in_array($locale, ['en', 'es'])) {
            
            // 2. Si está permitido, lo guarda en la sesión del usuario
            // (Esto usa la MISMA sesión donde guardas 'user_type')
            session()->put('locale', $locale);
        }
        
        // 3. Redirige al usuario de vuelta a la página donde estaba
        return redirect()->back();
    }
}