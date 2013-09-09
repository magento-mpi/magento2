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
 * HTTP response implementation that is used instead core one for testing
 */
class Magento_Test_Response extends Magento_Core_Controller_Response_Http
{
    /**
     *
     */
    function __construct()
    {
        parent::__construct(
            Magento_Test_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_Event_Manager')
        );
    }

    /**
     * Prevent generating exceptions if headers are already sent
     *
     * Prevents throwing an exception in Zend_Controller_Response_Abstract::canSendHeaders()
     * All functionality that depend on headers validation should be covered with unit tests by mocking response.
     *
     * @param bool $throw
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function canSendHeaders($throw = false)
    {
        return true;
    }

    public function sendResponse()
    {
        Mage::dispatchEvent('http_response_send_before', array('response'=>$this));
    }
}
