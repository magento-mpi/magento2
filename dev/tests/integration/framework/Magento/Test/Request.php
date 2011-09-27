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
 * HTTP request implementation that is used instead core one for testing
 */
class Magento_Test_Request extends Mage_Core_Controller_Request_Http
{
    /**
     * Retrieve HTTP HOST
     *
     * @param bool $trimPort
     * @return string
     */
    public function getHttpHost($trimPort = true)
    {
        return 'localhost';
    }
}
