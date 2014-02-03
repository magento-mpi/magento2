<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Payflow Direct payment block
 */
namespace Magento\Pbridge\Block\Adminhtml\Sales\Order\Create;

class PayflowDirect extends \Magento\Pbridge\Block\Adminhtml\Sales\Order\Create\Paypal
{
    /**
     * Payflow Direct payment code
     *
     * @var string
     */
    protected $_code = \Magento\Paypal\Model\Config::METHOD_WPP_PE_DIRECT;
}
