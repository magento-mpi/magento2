<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Model\Session;

/**
 * Mock headers_sent function to prevent check whether headers have been already sent
 *
 * @see \Magento\Core\Model\Session\AbstractSession
 */
function headers_sent()
{
    return false;
}

/**
 * Mock session_regenerate_id function to prevent check whether headers have been already sent
 *
 * @see \Magento\Core\Model\Session\AbstractSession
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
function session_regenerate_id($delete_old_session = false)
{
    $sessionId = hexdec(session_id());
    $sessionId++;  //Change Session Id value
    session_id(dechex($sessionId));
    return true;
}


/**
 * Mock session_start function to prevent check whether headers have been already sent
 *
 * @see \Magento\Core\Model\Session\AbstractSession
 */
function session_start()
{
    return true;
}
