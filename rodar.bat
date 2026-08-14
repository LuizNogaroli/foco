@echo off
title Foco-19 - Servidor Local de Testes
cd /d C:\dev\Foco-19

echo ==========================================
echo   Foco-19 - Servidor Local de Testes
echo ==========================================
echo.

REM Verifica se o PHP existe no PATH
where php >nul 2>nul
if errorlevel 1 goto sem_php

REM Verifica se o Composer existe no PATH
where composer >nul 2>nul
if errorlevel 1 goto sem_composer

REM Instala dependencias se vendor nao existir
if exist vendor goto composer_ok
echo [1/3] Instalando dependencias (composer install)
call composer install --no-interaction
if errorlevel 1 goto falha
goto composer_ok

:sem_php
echo [ERRO] PHP nao encontrado no PATH.
echo Instale o PHP e adicione o caminho ao PATH.
goto fim_erro

:sem_composer
echo [AVISO] Composer nao encontrado no PATH.
echo Pulando instalacao de dependencias.
goto composer_ok

:falha
echo [ERRO] Falha ao instalar as dependencias.
goto fim_erro

:composer_ok
echo [2/3] Aplicando migracoes (php artisan migrate)
call php artisan migrate --force

echo [3/3] Iniciando servidor em http://localhost:8000
echo.
echo Pressione Ctrl+C para encerrar o servidor.
echo.
php artisan serve
goto fim

:fim_erro
echo.
echo Processo encerrado com erros.
pause
exit /b 1

:fim
exit /b 0
