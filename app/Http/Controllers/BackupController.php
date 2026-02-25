<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use App\Helpers\Auditoria;
use Carbon\Carbon;

class BackupController extends Controller
{
    public function index()
    {
        // En Railway el filesystem es efímero, no se pueden listar backups guardados
        $files = collect([]);
        return view('backups.index', compact('files'));
    }

    public function generate()
    {
        try {
            $dbHost     = config('database.connections.mysql.host');
            $dbPort     = config('database.connections.mysql.port');
            $dbName     = config('database.connections.mysql.database');
            $dbUser     = config('database.connections.mysql.username');
            $dbPassword = config('database.connections.mysql.password');

            $mysqlBin  = '/usr/bin/mysqldump';
            $timestamp = Carbon::now('America/La_Paz')->format('Y-m-d-H-i-s');
            $sqlFile   = '/tmp/dump-' . $timestamp . '.sql';
            $zipFile   = '/tmp/emdell-backup-' . $timestamp . '.zip';

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

            $nombreArchivo = 'emdell-backup-' . $timestamp . '.zip';

            // ── AUDITORÍA ──
            Auditoria::registrar(
                'Respaldos',
                'Generar',
                'Generó y descargó un backup de la base de datos: "' . $nombreArchivo . '"'
            );

            // Descargar directamente y luego eliminar el temporal
            return response()->download($zipFile, $nombreArchivo, [
                'Content-Type' => 'application/zip',
            ])->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            return redirect()->route('backups.index')
                             ->with('error', 'Excepción: ' . $e->getMessage());
        }
    }

    // Download y delete ya no aplican en Railway (filesystem efímero)
    public function download($filename)
    {
        return redirect()->route('backups.index')
                         ->with('error', 'En este entorno los backups se descargan directamente al generarlos.');
    }

    public function delete($filename)
    {
        return redirect()->route('backups.index')
                         ->with('error', 'En este entorno los backups no se almacenan en el servidor.');
    }

    private function formatSize($bytes)
    {
        if ($bytes >= 1073741824) return number_format($bytes / 1073741824, 2) . ' GB';
        if ($bytes >= 1048576)    return number_format($bytes / 1048576,    2) . ' MB';
        if ($bytes >= 1024)       return number_format($bytes / 1024,       2) . ' KB';
        return $bytes . ' B';
    }
}