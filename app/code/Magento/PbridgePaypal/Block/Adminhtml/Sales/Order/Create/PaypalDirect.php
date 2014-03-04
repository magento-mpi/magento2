<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Paypal Direct payment block
 */
namespace Magento\PbridgePaypal\Block\Adminhtml\Sales\Order\Create;

class PaypalDirect extends \Magento\Pbridge\Block\Adminhtml\Sales\Order\Create\AbstractCreate
{
    /**
     * Paypal payment code
     *
     * @var string
     */
    protected $_code = \Magento\Paypal\Model\Config::METHOD_WPP_DIRECT;
}
