<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use App\Helpers\Auditoria;
use Carbon\Carbon;

class BackupController extends Controller
{
    protected $disk = 'local';
    protected $backupPath = 'private/emdell';

    public function index()
    {
        // DEBUG TEMPORAL
        $rutaReal = storage_path('app/' . $this->backupPath);
        $existeDir = file_exists($rutaReal);
        $archivos = $existeDir ? scandir($rutaReal) : [];

        $files = collect(Storage::disk($this->disk)->files($this->backupPath))
            ->filter(fn($f) => str_ends_with($f, '.zip'))
            ->map(function ($file) {
                return [
                    'nombre' => basename($file),
                    'ruta' => $file,
                    'tamaño' => $this->formatSize(Storage::disk($this->disk)->size($file)),
                    'fecha' => Carbon::createFromTimestamp(
                        Storage::disk($this->disk)->lastModified($file)
                    )->setTimezone('America/La_Paz')->format('d/m/Y H:i:s'),
                ];
            })
            ->sortByDesc('fecha')
            ->values();

        // Mostrar debug en pantalla
        dd([
            'backupPath' => $this->backupPath,
            'rutaReal' => $rutaReal,
            'existeDir' => $existeDir,
            'archivosDir' => $archivos,
            'filesStorage' => Storage::disk($this->disk)->files($this->backupPath),
        ]);

        return view('backups.index', compact('files'));
    }

    public function generate()
    {
        try {
            $dbHost = config('database.connections.mysql.host');
            $dbPort = config('database.connections.mysql.port');
            $dbName = config('database.connections.mysql.database');
            $dbUser = config('database.connections.mysql.username');
            $dbPassword = config('database.connections.mysql.password');

            $mysqlBin = '/usr/bin/mysqldump';
            $timestamp = Carbon::now('America/La_Paz')->format('Y-m-d-H-i-s');
            $sqlFile = storage_path("app/private/emdell/dump-{$timestamp}.sql");
            $zipFile = storage_path("app/private/emdell/emdell-backup-{$timestamp}.zip");

            if (!file_exists(storage_path('app/private/emdell'))) {
                mkdir(storage_path('app/private/emdell'), 0755, true);
            }

            $command = "{$mysqlBin} -h {$dbHost} -P {$dbPort} -u {$dbUser} " .
                ($dbPassword ? "-p\"{$dbPassword}\"" : "") .
                " --ssl=0" .
                " {$dbName} 2>/tmp/mysqldump_error.txt > \"{$sqlFile}\"";

            exec($command, $output, $exitCode);

            if ($exitCode !== 0 || !file_exists($sqlFile)) {
                $detalle = file_exists('/tmp/mysqldump_error.txt')
                    ? file_get_contents('/tmp/mysqldump_error.txt')
                    : 'sin detalle';
                return redirect()->route('backups.index')
                    ->with('error', 'Código: ' . $exitCode . ' | ' . $detalle);
            }

            $zip = new \ZipArchive();
            if ($zip->open($zipFile, \ZipArchive::CREATE) === true) {
                $zip->addFile($sqlFile, basename($sqlFile));
                $zip->close();
            }

            unlink($sqlFile);

            // ── AUDITORÍA ──
            $nombreArchivo = basename($zipFile);
            Auditoria::registrar(
                'Respaldos',
                'Generar',
                'Generó un nuevo backup de la base de datos: "' . $nombreArchivo . '"'
            );

            return redirect()->route('backups.index')
                ->with('success', 'Backup generado correctamente.');

        } catch (\Exception $e) {
            return redirect()->route('backups.index')
                ->with('error', 'Excepción: ' . $e->getMessage());
        }
    }

    public function download($filename)
    {
        $path = $this->backupPath . '/' . $filename;

        if (!Storage::disk($this->disk)->exists($path)) {
            abort(404, 'Archivo no encontrado.');
        }

        // ── AUDITORÍA ──
        Auditoria::registrar(
            'Respaldos',
            'Descargar',
            'Descargó el backup: "' . $filename . '"'
        );

        return response()->download(
            storage_path('app/' . $path),
            $filename,
            ['Content-Type' => 'application/zip']
        );
    }

    public function delete($filename)
    {
        $path = $this->backupPath . '/' . $filename;

        if (Storage::disk($this->disk)->exists($path)) {
            Storage::disk($this->disk)->delete($path);

            // ── AUDITORÍA ──
            Auditoria::registrar(
                'Respaldos',
                'Eliminar',
                'Eliminó el backup: "' . $filename . '"'
            );

            return redirect()->route('backups.index')
                ->with('success', 'Backup eliminado correctamente.');
        }

        return redirect()->route('backups.index')
            ->with('error', 'Archivo no encontrado.');
    }

    private function formatSize($bytes)
    {
        if ($bytes >= 1073741824)
            return number_format($bytes / 1073741824, 2) . ' GB';
        if ($bytes >= 1048576)
            return number_format($bytes / 1048576, 2) . ' MB';
        if ($bytes >= 1024)
            return number_format($bytes / 1024, 2) . ' KB';
        return $bytes . ' B';
    }
}