<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Paygate
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * @method Magento_Paygate_Model_Resource_Authorizenet_Debug _getResource()
 * @method Magento_Paygate_Model_Resource_Authorizenet_Debug getResource()
 * @method string getRequestBody()
 * @method Magento_Paygate_Model_Authorizenet_Debug setRequestBody(string $value)
 * @method string getResponseBody()
 * @method Magento_Paygate_Model_Authorizenet_Debug setResponseBody(string $value)
 * @method string getRequestSerialized()
 * @method Magento_Paygate_Model_Authorizenet_Debug setRequestSerialized(string $value)
 * @method string getResultSerialized()
 * @method Magento_Paygate_Model_Authorizenet_Debug setResultSerialized(string $value)
 * @method string getRequestDump()
 * @method Magento_Paygate_Model_Authorizenet_Debug setRequestDump(string $value)
 * @method string getResultDump()
 * @method Magento_Paygate_Model_Authorizenet_Debug setResultDump(string $value)
 *
 * @category    Magento
 * @package     Magento_Paygate
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Paygate_Model_Authorizenet_Debug extends Magento_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('Magento_Paygate_Model_Resource_Authorizenet_Debug');
    }
}
