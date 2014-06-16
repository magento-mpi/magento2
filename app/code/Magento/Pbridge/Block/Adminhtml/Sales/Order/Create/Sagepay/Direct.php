<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Pbridge\Block\Adminhtml\Sales\Order\Create\Sagepay;

class Direct extends \Magento\Pbridge\Block\Adminhtml\Sales\Order\Create\AbstractCreate
{
    /**
     * Whether to include shopping cart items parameters in Payment Bridge source URL
     *
     * @var bool
     */
    protected $_sendCart = true;
}
