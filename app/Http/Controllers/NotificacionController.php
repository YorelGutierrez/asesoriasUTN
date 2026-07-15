<?php

namespace App\Http\Controllers;

use App\Models\notificaciones;
use App\Models\sesiones_asesoria;
use Illuminate\Http\Request;

class NotificacionController extends Controller
{
    // Obtener notificaciones del usuario autenticado
    public function index()
    {
        $notificaciones = notificaciones::where('usuario_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return response()->json([
            'notificaciones' => $notificaciones,
            'no_leidas' => $notificaciones->where('leido', false)->count(),
        ]);
    }

    // Marcar una notificación individual como leída.
    public function marcarLeida($id)
    {
        $notif = notificaciones::where('id', $id)
            ->where('usuario_id', auth()->id())
            ->firstOrFail();

        $notif->update(['leido' => true]);

        return response()->json(['ok' => true]);
    }

    // Marcar una notificación como leída
    public function marcarTodasLeidas()
    {
        notificaciones::where('usuario_id', auth()->id())
            ->where('leido', false)
            ->update(['leido' => true]);

        return response()->json(['ok' => true]);
    }

    public function confirmar($id)
    {
        $notif = notificaciones::where('id', $id)
            ->where('usuario_id', auth()->id())
            ->firstOrFail();

        // Solo docentes pueden confirmar
        if (auth()->user()->rol !== 'docente') {
            return response()->json(['ok' => false, 'mensaje' => 'No autorizado'], 403);
        }

        $notif->update(['accion' => 'confirmada', 'leido' => true]);

        if (!empty($notif->datos['sesion_id'])) {
            sesiones_asesoria::where('id', $notif->datos['sesion_id'])
                ->update(['estado' => 'programada']);
        }

        // NOTIFICAR AL ALUMNO QUE LA ASESORÍA FUE CONFIRMADA
        if (!empty($notif->datos['alumno_user_id'])) {
            $alumnoId = $notif->datos['alumno_user_id'];
            $sesionId = $notif->datos['sesion_id'] ?? null;
            $fecha = $notif->datos['fecha'] ?? '';
            $hora = $notif->datos['hora'] ?? '';
            $tema = $notif->datos['tema'] ?? '';

            notificaciones::crear(
                $alumnoId,
                'confirmacion',
                "Tu asesoría sobre \"{$tema}\" ha sido confirmada por el docente para el {$fecha} a las {$hora}. ¡Listo para la sesión!",
                [
                    'sesion_id' => $sesionId,
                    'tipo' => 'confirmacion_docente',
                ]
            );
        }

        return response()->json(['ok' => true, 'mensaje' => 'Asesoría confirmada.']);
    }

    public function rechazar($id)
    {
        $notif = notificaciones::where('id', $id)
            ->where('usuario_id', auth()->id())
            ->firstOrFail();

        // Solo docentes pueden rechazar
        if (auth()->user()->rol !== 'docente') {
            return response()->json(['ok' => false, 'mensaje' => 'No autorizado'], 403);
        }

        $notif->update(['accion' => 'rechazada', 'leido' => true]);

        if (!empty($notif->datos['sesion_id'])) {
            sesiones_asesoria::where('id', $notif->datos['sesion_id'])
                ->update(['estado' => 'cancelada']);
        }

        // NOTIFICAR AL ALUMNO QUE LA ASESORÍA FUE RECHAZADA
        if (!empty($notif->datos['alumno_user_id'])) {
            $alumnoId = $notif->datos['alumno_user_id'];
            $sesionId = $notif->datos['sesion_id'] ?? null;
            $fecha = $notif->datos['fecha'] ?? '';
            $hora = $notif->datos['hora'] ?? '';
            $tema = $notif->datos['tema'] ?? '';

            notificaciones::crear(
                $alumnoId,
                'rechazo',
                "Tu asesoría sobre \"{$tema}\" programada para el {$fecha} a las {$hora} no pudo ser confirmada. El docente se pondrá en contacto contigo para reagendar.",
                [
                    'sesion_id' => $sesionId,
                    'tipo' => 'rechazo_docente',
                ]
            );
        }

        return response()->json(['ok' => true, 'mensaje' => 'Solicitud rechazada.']);
    }
}
