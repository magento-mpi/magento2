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
 * @method \Magento\Paygate\Model\Resource\Authorizenet\Debug _getResource()
 * @method \Magento\Paygate\Model\Resource\Authorizenet\Debug getResource()
 * @method string getRequestBody()
 * @method \Magento\Paygate\Model\Authorizenet\Debug setRequestBody(string $value)
 * @method string getResponseBody()
 * @method \Magento\Paygate\Model\Authorizenet\Debug setResponseBody(string $value)
 * @method string getRequestSerialized()
 * @method \Magento\Paygate\Model\Authorizenet\Debug setRequestSerialized(string $value)
 * @method string getResultSerialized()
 * @method \Magento\Paygate\Model\Authorizenet\Debug setResultSerialized(string $value)
 * @method string getRequestDump()
 * @method \Magento\Paygate\Model\Authorizenet\Debug setRequestDump(string $value)
 * @method string getResultDump()
 * @method \Magento\Paygate\Model\Authorizenet\Debug setResultDump(string $value)
 *
 * @category    Magento
 * @package     Magento_Paygate
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Paygate\Model\Authorizenet;

class Debug extends \Magento\Core\Model\AbstractModel
{
    protected function _construct()
    {
        $this->_init('Magento\Paygate\Model\Resource\Authorizenet\Debug');
    }
}
