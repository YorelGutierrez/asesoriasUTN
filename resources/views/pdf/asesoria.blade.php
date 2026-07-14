<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Asesoría</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid rgb(17, 160, 139); padding-bottom: 10px; }
        .header h1 { color: rgb(17, 160, 139); margin: 0; }
        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .info-table td, .info-table th { border: 1px solid #ddd; padding: 8px; }
        .info-table th { background-color: rgb(17, 160, 139); color: white; width: 30%; }
        .alumnos-table { width: 100%; border-collapse: collapse; }
        .alumnos-table th, .alumnos-table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .alumnos-table th { background-color: #343a40; color: white; text-align: center; }
        .footer { margin-top: 30px; text-align: center; font-size: 10px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Registro de Asesoría Académica</h1>
        <p>Universidad Tecnológica de Nayarit</p>
    </div>

    <table class="info-table">
        <tr><th>Carrera:</th><td>{{ $data['carrera_nombre'] }}</td></tr>
        <tr><th>Tipo de asesoría:</th><td>{{ ucfirst($data['tipo_asesoria']) }}</td></tr>
        <tr><th>Asignatura:</th><td>{{ $data['materia_nombre'] }}</td></tr>
        <tr><th>Motivo:</th><td>{{ $data['motivo'] }}</td></tr>
        <tr><th>Tema:</th><td>{{ $data['tema'] }}</td></tr>
        <tr><th>Modalidad:</th><td>{{ $data['modalidad'] }}</td></tr>
        <tr><th>Fecha:</th><td>{{ $data['fecha'] }}</td></tr>
        <tr><th>Horario:</th><td>{{ $data['hora_inicio'] }} - {{ $data['hora_fin'] }}</td></tr>
    </table>

    <h3>Lista de Alumnos</h3>
    <table class="alumnos-table">
        <thead>
            <tr><th>#</th><th>Nombre del Alumno</th><th>Grupo</th><th>Firma</th></tr>
        </thead>
        <tbody>
            @foreach($data['alumnos'] as $index => $alumno)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $alumno['nombre'] }}</td>
                <td>{{ $alumno['grupo'] }}</td>
                <td style="height: 30px;"></td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Documento generado el {{ date('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html>