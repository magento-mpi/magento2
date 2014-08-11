<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Session;

use \Magento\Backend\Model\SessionTest;

function headers_sent()
{
    return false;
}

function session_status()
{
    return PHP_SESSION_NONE;
}

function session_start()
{
    SessionTest::$sessionStart = true;
}

function register_shutdown_function()
{
    SessionTest::$registerShutdownFunction = true;
}
