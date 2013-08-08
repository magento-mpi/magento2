<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Paygate
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * @method Mage_Paygate_Model_Resource_Authorizenet_Debug _getResource()
 * @method Mage_Paygate_Model_Resource_Authorizenet_Debug getResource()
 * @method string getRequestBody()
 * @method Mage_Paygate_Model_Authorizenet_Debug setRequestBody(string $value)
 * @method string getResponseBody()
 * @method Mage_Paygate_Model_Authorizenet_Debug setResponseBody(string $value)
 * @method string getRequestSerialized()
 * @method Mage_Paygate_Model_Authorizenet_Debug setRequestSerialized(string $value)
 * @method string getResultSerialized()
 * @method Mage_Paygate_Model_Authorizenet_Debug setResultSerialized(string $value)
 * @method string getRequestDump()
 * @method Mage_Paygate_Model_Authorizenet_Debug setRequestDump(string $value)
 * @method string getResultDump()
 * @method Mage_Paygate_Model_Authorizenet_Debug setResultDump(string $value)
 *
 * @category    Mage
 * @package     Mage_Paygate
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Paygate_Model_Authorizenet_Debug extends Magento_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('Mage_Paygate_Model_Resource_Authorizenet_Debug');
    }
}
