<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_SalesArchive
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Archive orders block
 *
 */

namespace Magento\SalesArchive\Block\Adminhtml\Sales\Archive;

class Order extends \Magento\SalesArchive\Block\Adminhtml\Sales\Archive\Order\Container
{
    protected function _construct()
    {
        $this->_controller = 'sales_order';
        $this->_headerText = __('Orders Archive');
    }
}
