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
class Magento_Test_Response extends Mage_Core_Controller_Response_Http
{
    /**
     * Prevent generating exceptions if headers are already sent
     *
     * Prevents throwing an exception in the following places:
     * Zend_Controller_Response_Abstract::canSendHeaders()
     * Mage_Core_Model_Cookie::set()
     * Mage_Core_Model_Cookie::delete()
     *
     * All functionality that depend on headers validation should be covered with unit tests by mocking response.
     *
     * @param bool $throw
     * @return bool
     */
    public function canSendHeaders($throw = false)
    {
        /**
         * Don't allow Mage_Core_Model_Cookie to set/delete cookies,
         * because headers were sent by integration testing environment.
         *
         * This specific condition is used just because the Mage_Core_Model_Cookie happens to be the only code
         * that specifies $throw argument explicitly with false value, so we can identify and hack it.
         */
        if (1 === func_num_args() && false === $throw) {
            return false;
        }
        return true;
    }

    public function sendResponse()
    {
        Mage::dispatchEvent('http_response_send_before', array('response'=>$this));
    }
}
