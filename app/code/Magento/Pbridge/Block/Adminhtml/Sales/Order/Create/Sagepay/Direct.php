<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
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
