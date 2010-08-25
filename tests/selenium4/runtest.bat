@echo off
cls

echo **************************************************************************
echo **
echo **  Selenium TestCase Framework
echo **  Version 0.4.1
echo **  Copyright (c) Magento Inc.
echo **
echo **************************************************************************
echo.

:: Uncomment the following line if you want to run the framework for a specific 
:: environment

:: set SELENIUM_ENV=test

:: Uncomment the following line if you want to force certain debug level.
:: valid values are OFF, INFO, DEBUG, and ERROR

:: set SELENIUM_DEBUG_LEVEL=DEBUG

:: ****************************************************************************

if "%1"=="" goto UsageManual

php %~dp0\testloader.php %*

echo.
echo --------------------------------------------------------------------------
echo Done
echo.

goto End

:: ****************************************************************************

:UsageManual

echo Usage: 
echo   runtest ^<testCaseFile1^> ^[ ^<testCaseFile2^> ... ^[ ^<testCaseFileN^> ^] ^]
echo or
echo   runtest ^<testCaseID1^> ^[ ^<testCaseID2^> ... ^[ ^<testCaseIDN^> ^] ^]
echo where:
echo   - ^<testCaseFile^> is a PHP file that contains a test case
echo   - ^<testCaseID^> is an ID corresponding a test case
echo.

:End