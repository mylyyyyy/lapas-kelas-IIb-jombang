# NSSM Instructions (Windows)

1. Download NSSM: https://nssm.cc/download and extract to `C:\tools\nssm`.
2. Open PowerShell as Administrator.
3. Install service (example) using this repository path (`C:\Tugas Kuliah\Belajar\lapas-jombang`):

```powershell
# (adjust path to php.exe if necessary, e.g. C:\php\php.exe)
$php = "php"
$project = "C:\Tugas Kuliah\Belajar\lapas-jombang"
$artisanArgs = "${project}\artisan queue:work database --sleep=3 --tries=3 --timeout=90"

C:\tools\nssm\nssm.exe install laravel-queue
C:\tools\nssm\nssm.exe set laravel-queue Application $php
C:\tools\nssm\nssm.exe set laravel-queue AppParameters $artisanArgs
C:\tools\nssm\nssm.exe set laravel-queue AppDirectory $project
C:\tools\nssm\nssm.exe set laravel-queue AppStdout "$project\storage\logs\worker.log"
C:\tools\nssm\nssm.exe set laravel-queue AppStderr "$project\storage\logs\worker-error.log"
C:\tools\nssm\nssm.exe set laravel-queue Start SERVICE_AUTO_START
C:\tools\nssm\nssm.exe start laravel-queue
```

4. To restart service during deploy, use:
```powershell
C:\tools\nssm\nssm.exe restart laravel-queue
```

5. Notes:
- Ensure the service account has write access to project logs and storage directories.
- Use `php artisan queue:restart` in deployment scripts to gracefully reload workers when code changes.
- Use `where php` to confirm which PHP executable to reference if `php` is not in PATH.
