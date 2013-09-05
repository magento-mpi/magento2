<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Bootstrap of the HTTP environment
 */
class Magento_TestFramework_Bootstrap_Environment
{
    /**
     * Emulate properties typical to an HTTP request
     *
     * @param array $serverVariables
     */
    public function emulateHttpRequest(array &$serverVariables)
    {
        // emulate HTTP request
        $serverVariables['HTTP_HOST'] = 'localhost';
        // emulate entry point to ensure that tests generate invariant URLs
        $serverVariables['SCRIPT_FILENAME'] = 'index.php';
    }

    /**
     * Emulate already started PHP session
     *
     * @param array|null $sessionVariables
     */
    public function emulateSession(&$sessionVariables)
    {
        // prevent session_start, because it may rely on cookies
        $sessionVariables = array();
        // application relies on a non-empty session ID
        session_id(uniqid());
    }
}
