<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_GoogleCheckout
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * @method Mage_GoogleCheckout_Model_Resource_Api_Debug _getResource()
 * @method Mage_GoogleCheckout_Model_Resource_Api_Debug getResource()
 * @method string getDir()
 * @method Mage_GoogleCheckout_Model_Api_Debug setDir(string $value)
 * @method string getUrl()
 * @method Mage_GoogleCheckout_Model_Api_Debug setUrl(string $value)
 * @method string getRequestBody()
 * @method Mage_GoogleCheckout_Model_Api_Debug setRequestBody(string $value)
 * @method string getResponseBody()
 * @method Mage_GoogleCheckout_Model_Api_Debug setResponseBody(string $value)
 *
 * @category    Mage
 * @package     Mage_GoogleCheckout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_GoogleCheckout_Model_Api_Debug extends Magento_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('Mage_GoogleCheckout_Model_Resource_Api_Debug');
    }
}
