<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class BackupDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Safely dump and compress the MySQL database for CTPF inventory, rotating files automatically after 7 days.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting CTPF Database Backup...');

        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');
        $host = config('database.connections.mysql.host');
        $port = config('database.connections.mysql.port');

        $backupDir = storage_path('app/backups');
        if (!File::exists($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }

        $filename = "ctpf_backup_" . date('Y_m_d_H_i_s') . ".sql";
        $filePath = $backupDir . '/' . $filename;

        // Command to dump database (compatible with Windows/Laragon and standard Linux/Hostinger servers)
        $passwordOption = $password ? "-p\"{$password}\"" : "";
        $command = "mysqldump -h {$host} -P {$port} -u {$username} {$passwordOption} {$database} > \"{$filePath}\"";

        // Execute command
        exec($command, $output, $resultCode);

        if ($resultCode === 0) {
            $this->info("Backup successfully generated: {$filename}");

            // Compress file
            if (File::exists($filePath)) {
                $sqlContent = File::get($filePath);
                $gzContent = gzencode($sqlContent, 9);
                File::put($filePath . '.gz', $gzContent);
                File::delete($filePath); // Delete raw .sql
                $this->info("Compressed backup created: {$filename}.gz");
            }

            // Keep only the last 7 backups (Rotate)
            $files = File::files($backupDir);
            usort($files, function ($a, $b) {
                return filemtime($b) <=> filemtime($a); // Descending order of modification time
            });

            if (count($files) > 7) {
                $filesToDelete = array_slice($files, 7);
                foreach ($filesToDelete as $file) {
                    File::delete($file);
                    $this->warn("Rotated old backup file: " . $file->getFilename());
                }
            }

            $this->info('Database backup rotation completed successfully.');
        } else {
            $this->error('Failed to generate database dump. Ensure mysqldump path is in system environment variables.');
        }
    }
}
