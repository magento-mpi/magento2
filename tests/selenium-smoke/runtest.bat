@echo off
cls

echo **************************************************************************
echo **
echo **  Selenium TestCase Framework
echo **  Version 0.3.1
echo **  Copyright (c) Magento Inc.
echo **
echo **************************************************************************
echo.

rem set LIBPATH="./tests"
set LIBPATH="D:/Trunk/tests/selenium-smoke"

if exist %LIBPATH% goto Ok
echo ERROR: LIBPATH path doesn't exists!
goto Ex

:Ok
if "%1"=="" goto NoTests
rem echo %*
call phpunit --bootstrap ./testloader.php --include-path %LIBPATH% --loader TestSuiteLoader %*
goto Ex

:NoTests
echo ERROR: No PHPUnit tests have been provided

:Ex
echo Done
