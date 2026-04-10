<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Asesoría Académica</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .header { text-align: center; margin-bottom: 30px; }
        .info { margin-bottom: 20px; }
        .label { font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Registro de Asesoría Académica</h1>
        <p>Universidad Tecnológica de Nayarit</p>
    </div>
    
    <div class="info">
        <p><span class="label">Docente:</span> {{ $data['docente_nombre'] }}</p>
        <p><span class="label">Carrera:</span> {{ $data['carrera_nombre'] }}</p>
        <p><span class="label">Fecha:</span> {{ $data['fecha'] }}</p>
        <p><span class="label">Hora:</span> {{ $data['hora_inicio'] }}</p>
        <p><span class="label">Modalidad:</span> {{ $data['modalidad'] }}</p>
        <p><span class="label">Tema:</span> {{ $data['tema'] }}</p>
        <p><span class="label">Objetivo:</span> {{ $data['pregunta_objetivo'] }}</p>
    </div>
</body>
</html>