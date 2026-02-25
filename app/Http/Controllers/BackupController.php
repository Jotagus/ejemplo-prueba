<?php

namespace App\Http\Controllers;

use App\Models\Backup;
use App\Helpers\Auditoria;
use Carbon\Carbon;

class BackupController extends Controller
{
    public function index()
    {
        $files = Backup::orderByDesc('created_at')->get()->map(function ($backup) {
            return [
                'nombre' => $backup->file_name,
                'tamaño' => $this->formatSize($backup->file_size),
                'fecha' => Carbon::parse($backup->created_at)
                    ->setTimezone('America/La_Paz')
                    ->format('d/m/Y H:i:s'),
            ];
        });

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
            $fileName = 'emdell-backup-' . $timestamp . '.zip';
            $sqlFile = '/tmp/dump-' . $timestamp . '.sql';
            $zipFile = '/tmp/' . $fileName;

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

            $fileSize = filesize($zipFile);

            // Guardar historial en la base de datos
            Backup::create([
                'file_name' => $fileName,
                'file_path' => $zipFile,
                'file_size' => $fileSize,
            ]);

            // ── AUDITORÍA ──
            Auditoria::registrar(
                'Respaldos',
                'Generar',
                'Generó y descargó un backup de la base de datos: "' . $fileName . '"'
            );

            return response()->download($zipFile, $fileName, [
                'Content-Type' => 'application/zip',
            ])->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            return redirect()->route('backups.index')
                ->with('error', 'Excepción: ' . $e->getMessage());
        }
    }

    public function download($filename)
    {
        return redirect()->route('backups.index')
            ->with('error', 'En este entorno los backups se descargan directamente al generarlos.');
    }

    public function delete($filename)
    {
        $backup = Backup::where('file_name', $filename)->first();

        if ($backup) {
            $backup->delete();

            // ── AUDITORÍA ──
            Auditoria::registrar(
                'Respaldos',
                'Eliminar',
                'Eliminó el registro de backup: "' . $filename . '"'
            );

            return redirect()->route('backups.index')
                ->with('success', 'Registro de backup eliminado correctamente.');
        }

        return redirect()->route('backups.index')
            ->with('error', 'Registro no encontrado.');
    }

    private function formatSize($bytes)
    {
        if (!$bytes)
            return '—';
        if ($bytes >= 1073741824)
            return number_format($bytes / 1073741824, 2) . ' GB';
        if ($bytes >= 1048576)
            return number_format($bytes / 1048576, 2) . ' MB';
        if ($bytes >= 1024)
            return number_format($bytes / 1024, 2) . ' KB';
        return $bytes . ' B';
    }
}