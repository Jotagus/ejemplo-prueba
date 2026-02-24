<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Kardex Material</title>
    <style>
        @page { size: legal landscape; margin: 0.5cm; }
        body { font-family: Arial, sans-serif; font-size: 8pt; }
        /* Usar los mismos estilos del kardex_pdf.blade.php existente */
    </style>
</head>
<body>
    @include('movimientos.kardex_pdf', [
        'material' => $material,
        'movimientos' => $movimientos
    ])
</body>
</html>