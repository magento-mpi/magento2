@echo off
rem --------------------------------------
rem
rem Please specify real path to tests folder in the LIBPATH variable
rem Usage: runtest.bat Admin/Customer/Address/Add.php 
rem 
rem --------------------------------------
cls
echo **************************************************************************
echo **
echo **  Selenium TestCase Framework
echo **  Version 0.1.2
echo **  Copyright (c) Magento Inc.
echo **
echo **************************************************************************
set LIBPATH="./tests"
if exist %LIBPATH% goto Ok
echo ERROR: LIBPATH path doesn't exists!
goto Ex
:Ok
if "%1"=="" goto NoTests
echo %*
call phpunit --bootstrap ./testloader.php --include-path %LIBPATH% %*
goto Ex
:NoTests
echo ERROR: No PHPUnit tests have been provided
:Ex
echo Done
