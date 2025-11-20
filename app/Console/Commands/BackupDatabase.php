<?php
namespace App\Console\Commands;

use App\Notifications\BackupStatusNotification;
use Carbon\Carbon;
use Google_Client;
use Google_Service_Drive;
use Google_Service_Drive_DriveFile;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

class BackupDatabase extends Command
{
    protected $signature   = 'backup:database';
    protected $description = 'Backup database to Google Drive';

    public function handle()
    {
        try {
            if (! Storage::has('app/DbBackup')) {
                Storage::makeDirectory('app/DbBackup');
            }
            $dbUsername = env('DB_USERNAME');
            $dbPassword = env('DB_PASSWORD');
            $dbHost     = env('DB_HOST');
            $dbName     = env('DB_DATABASE');
            $filename   = Carbon::now()->format('Y-m-d-is') . '_backup.sql';

            $escapedUsername = escapeshellarg($dbUsername);
            $escapedPassword = escapeshellarg($dbPassword);
            $escapedHost     = escapeshellarg($dbHost);
            $escapedDbName   = escapeshellarg($dbName);
            $folderPath      = storage_path('app/DbBackup/');
            if (! File::exists($folderPath)) {
                File::makeDirectory($folderPath, 0755, true);
            }
            $filename        = 'autolab_db_' . now()->format('Y-m-d_H-i-s') . '.sql';
            $fullPath        = $folderPath . $filename;   // unquoted for PHP
            $escapedFullPath = escapeshellarg($fullPath); // quoted for shell

            $command = "mysqldump --user={$escapedUsername} --password={$escapedPassword} --host={$escapedHost} {$escapedDbName} > {$escapedFullPath}";
            exec($command, $output, $returnVar);

            if (! file_exists($fullPath)) { // use unquoted path
                throw new \Exception('Backup file was not created');
                unlink($fullPath);
            }

            $credPath = Storage::path('app/google-credentials.json');
            if (! file_exists($credPath)) {
                throw new \Exception("Google credentials file not found at: $credPath");
                unlink($fullPath);
            }

            // Upload to Google Drive
            $client = new Google_Client();
            $client->setAuthConfig(Storage::path('app/google-credentials.json'));
            $client->addScope(Google_Service_Drive::DRIVE_FILE);

            $service = new Google_Service_Drive($client);

            $fileMetadata = new Google_Service_Drive_DriveFile([
                'name'    => $filename,
                'parents' => [env('GOOGLE_DRIVE_FOLDER_ID')],
            ]);

            $content = file_get_contents($fullPath); // use unquoted path
            $file    = $service->files->create($fileMetadata, [
                'data'       => $content,
                'mimeType'   => 'application/sql',
                'uploadType' => 'multipart',
            ]);

            $sizeInBytes = filesize($fullPath);
            $newestSize  = $fullPath ? number_format($sizeInBytes / 1024 / 1024, 2) . ' MB' : 'N/A';
            // Delete local backup
            unlink($fullPath);

            // Delete old backups from Google Drive
            $this->deleteOldBackups($service);

            $this->info('Database backup successfully uploaded to Google Drive');
            Log::info('Database backup completed successfully');
            Notification::route('mail', env('MAIL_ADMIN_ADDRESS', 'ict.makbrc@gmail.com'))
                ->notify(new BackupStatusNotification('success', $filename . ' Size ' . $newestSize));
            return 0;
        } catch (\Exception $e) {
            Log::error('Backup failed: ' . $e->getMessage());
            $this->error('Backup failed: ' . $e->getMessage());
            unlink($fullPath);
            Notification::route('mail', env('MAIL_ADMIN_ADDRESS', 'ict.makbrc@gmail.com'))
                ->notify(new BackupStatusNotification('failed', $e->getMessage()));
            return 1;
        }

    }

    protected function deleteOldBackups($service)
    {
        try {
            $retentionDays = Config::get('backup.google.retention_days');
            $cutoffDate    = Carbon::now()->subDays($retentionDays)->startOfDay();

            $results = $service->files->listFiles([
                'q'      => "'" . Config::get('backup.google.folder_id') . "' in parents and mimeType='application/sql'",
                'fields' => 'files(id, name, createdTime)',
            ]);

            foreach ($results->getFiles() as $file) {
                $createdTime = Carbon::parse($file->getCreatedTime());
                if ($createdTime->lt($cutoffDate)) {
                    $service->files->delete($file->getId());
                    Log::info("Deleted old backup: " . $file->getName());
                }
            }
        } catch (\Exception $e) {
            Log::warning('Failed to cleanup old backups: ' . $e->getMessage());
        }
    }
}
