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
 * @method \Magento\GoogleCheckout\Model\Resource\Api\Debug _getResource()
 * @method \Magento\GoogleCheckout\Model\Resource\Api\Debug getResource()
 * @method string getDir()
 * @method \Magento\GoogleCheckout\Model\Api\Debug setDir(string $value)
 * @method string getUrl()
 * @method \Magento\GoogleCheckout\Model\Api\Debug setUrl(string $value)
 * @method string getRequestBody()
 * @method \Magento\GoogleCheckout\Model\Api\Debug setRequestBody(string $value)
 * @method string getResponseBody()
 * @method \Magento\GoogleCheckout\Model\Api\Debug setResponseBody(string $value)
 *
 * @category    Magento
 * @package     Magento_GoogleCheckout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\GoogleCheckout\Model\Api;

class Debug extends \Magento\Core\Model\AbstractModel
{
    protected function _construct()
    {
        $this->_init('Magento\GoogleCheckout\Model\Resource\Api\Debug');
    }
}
