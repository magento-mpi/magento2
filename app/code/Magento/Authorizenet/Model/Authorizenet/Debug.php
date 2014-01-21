<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Authorizenet
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * @method \Magento\Authorizenet\Model\Resource\Authorizenet\Debug _getResource()
 * @method \Magento\Authorizenet\Model\Resource\Authorizenet\Debug getResource()
 * @method string getRequestBody()
 * @method \Magento\Authorizenet\Model\Authorizenet\Debug setRequestBody(string $value)
 * @method string getResponseBody()
 * @method \Magento\Authorizenet\Model\Authorizenet\Debug setResponseBody(string $value)
 * @method string getRequestSerialized()
 * @method \Magento\Authorizenet\Model\Authorizenet\Debug setRequestSerialized(string $value)
 * @method string getResultSerialized()
 * @method \Magento\Authorizenet\Model\Authorizenet\Debug setResultSerialized(string $value)
 * @method string getRequestDump()
 * @method \Magento\Authorizenet\Model\Authorizenet\Debug setRequestDump(string $value)
 * @method string getResultDump()
 * @method \Magento\Authorizenet\Model\Authorizenet\Debug setResultDump(string $value)
 *
 * @category    Magento
 * @package     Magento_Authorizenet
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Authorizenet\Model\Authorizenet;

class Debug extends \Magento\Core\Model\AbstractModel
{
    protected function _construct()
    {
        $this->_init('Magento\Authorizenet\Model\Resource\Authorizenet\Debug');
    }
}
