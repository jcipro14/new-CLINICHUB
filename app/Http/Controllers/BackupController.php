<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\AuditLog;

class BackupController extends Controller
{
    public function index()
    {
        $backupHistory = AuditLog::where('action', 'Download Backup')
            ->orderByDesc('timestamp')
            ->limit(10)
            ->get();

        return view('superadmin.backup', compact('backupHistory'));
    }

    public function download()
    {
        $tables = DB::select('SHOW TABLES');
        $dbName = config('database.connections.mysql.database');
        $key    = 'Tables_in_' . $dbName;

        $sql = "-- ClinicHub Database Backup\n-- Generated: " . now() . "\n\nSET FOREIGN_KEY_CHECKS=0;\n\n";

        foreach ($tables as $tableObj) {
            $table = $tableObj->$key;

            // Drop + create
            $createResult = DB::select("SHOW CREATE TABLE `{$table}`");
            $createSql    = $createResult[0]->{'Create Table'};
            $sql .= "DROP TABLE IF EXISTS `{$table}`;\n{$createSql};\n\n";

            // Data
            $rows = DB::table($table)->get();
            foreach ($rows as $row) {
                $columns = array_keys((array) $row);
                $values  = array_map(fn($v) => is_null($v) ? 'NULL' : "'" . addslashes($v) . "'", (array) $row);
                $sql .= "INSERT INTO `{$table}` (`" . implode('`, `', $columns) . "`) VALUES (" . implode(', ', $values) . ");\n";
            }
            $sql .= "\n";
        }

        $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";

        $filename = 'clinichub_backup_' . now()->format('Ymd_His') . '.sql';

        AuditLog::record(
            Auth::user()->id_number,
            'superadmin',
            'Download Backup',
            "Downloaded backup: {$filename}"
        );

        return response($sql, 200, [
            'Content-Type'        => 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
