@echo off
title Fixsmith PC Repair Studio - Laravel Server
echo ========================================================
echo    Fixsmith - PC Repair Studio (Laravel Application)
echo ========================================================
echo.

if exist "%USERPROFILE%\.config\herd-lite\bin\php.exe" (
    set "PATH=%USERPROFILE%\.config\herd-lite\bin;%PATH%"
)

cd /d "%~dp0"
echo Starting Laravel web server at http://127.0.0.1:8000 ...
echo Press Ctrl+C to stop the server.
echo.
start http://127.0.0.1:8000
php artisan serve --port=8000
pause

