<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GoogleCheckout
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * @method Magento_GoogleCheckout_Model_Resource_Api_Debug _getResource()
 * @method Magento_GoogleCheckout_Model_Resource_Api_Debug getResource()
 * @method string getDir()
 * @method Magento_GoogleCheckout_Model_Api_Debug setDir(string $value)
 * @method string getUrl()
 * @method Magento_GoogleCheckout_Model_Api_Debug setUrl(string $value)
 * @method string getRequestBody()
 * @method Magento_GoogleCheckout_Model_Api_Debug setRequestBody(string $value)
 * @method string getResponseBody()
 * @method Magento_GoogleCheckout_Model_Api_Debug setResponseBody(string $value)
 *
 * @category    Magento
 * @package     Magento_GoogleCheckout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_GoogleCheckout_Model_Api_Debug extends Magento_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('Magento_GoogleCheckout_Model_Resource_Api_Debug');
    }
}
