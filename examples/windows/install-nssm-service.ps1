# Install NSSM service for Laravel queue (example)
# Usage: Run PowerShell as Administrator and adjust $PhpPath if needed

$PhpPath = "php" # or full path "C:\php\php.exe"
$Project = "C:\Tugas Kuliah\Belajar\lapas-jombang"
$ServiceName = "laravel-queue"
$Nssm = "C:\tools\nssm\nssm.exe"

if (-not (Test-Path $Nssm)) {
    Write-Error "NSSM not found at $Nssm. Download and extract NSSM to C:\tools\nssm first."
    exit 1
}

$Args = """$Project\artisan""" + " queue:work database --sleep=3 --tries=3 --timeout=90"

& $Nssm install $ServiceName
& $Nssm set $ServiceName Application $PhpPath
& $Nssm set $ServiceName AppParameters $Args
& $Nssm set $ServiceName AppDirectory $Project
& $Nssm set $ServiceName AppStdout "$Project\storage\logs\worker.log"
& $Nssm set $ServiceName AppStderr "$Project\storage\logs\worker-error.log"
& $Nssm set $ServiceName Start SERVICE_AUTO_START

Write-Host "Service $ServiceName installed. Starting service..."
& $Nssm start $ServiceName

Write-Host "Done. Check logs in $Project\storage\logs\worker.log"