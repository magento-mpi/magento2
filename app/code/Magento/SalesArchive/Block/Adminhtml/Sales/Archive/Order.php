<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\SalesArchive\Block\Adminhtml\Sales\Archive;

/**
 * Archive orders block
 */
class Order extends \Magento\SalesArchive\Block\Adminhtml\Sales\Archive\Order\Container
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'sales_order';
        $this->_headerText = __('Orders Archive');
    }
}
