<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Session;
use \Magento\Backend\Model\SessionTest;

function headers_sent()
{
    return false;
}

function session_status()
{
    return PHP_SESSION_NONE;
}

function session_name($name)
{
    SessionTest::assertEquals($name, 'adminhtml');
}

function session_start()
{
    SessionTest::$sessionStart = true;
}

function register_shutdown_function()
{
    SessionTest::$registerShutdownFunction = true;
}
