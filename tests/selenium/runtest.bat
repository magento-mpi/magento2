rem --------------------------------------
rem
rem Please specify real path to bricks folder in the LIBPATH variable
rem Usage: runtest.bat cu_add_address.php 
rem 
rem --------------------------------------
echo off
cls
set LIBPATH="C:\Daily\Selenium\TestCases\PHP\AdminCustomerManageAddress\PHP\Lib"
echo %*
if exist %LIBPATH% goto Ok
Echo LIBPATH path doesn't exists!
goto Ex
:Ok
if "%1"=="" goto NoTests
call phpunit --bootstrap ./testloader.php --include-path %LIBPATH% %*
goto Ex
:NoTests
Echo No PHP-Unit tests were provided
:Ex
echo Done