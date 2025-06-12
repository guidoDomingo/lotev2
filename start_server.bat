@echo off
echo ================================================
echo    INICIANDO SERVIDOR LARAVEL - TOUR 360
echo ================================================
echo.
echo Servidor iniciado en: http://127.0.0.1:8000
echo.
echo Rutas disponibles:
echo   - http://127.0.0.1:8000/             (Página principal)
echo   - http://127.0.0.1:8000/hotspots     (Gestión de hotspots)
echo   - http://127.0.0.1:8000/viewer       (Visor 360° interactivo)
echo   - http://127.0.0.1:8000/test-api     (Test de API)
echo.
echo Presiona Ctrl+C para detener el servidor
echo ================================================
echo.

cd /d "%~dp0"
php artisan serve --host=127.0.0.1 --port=8000
