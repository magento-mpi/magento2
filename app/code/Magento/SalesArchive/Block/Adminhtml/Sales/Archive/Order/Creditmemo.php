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
 * Archive shipment block
 *
 */

namespace Magento\SalesArchive\Block\Adminhtml\Sales\Archive\Order;

class Creditmemo
    extends \Magento\SalesArchive\Block\Adminhtml\Sales\Archive\Order\Container
{
    protected function _construct()
    {
        $this->_controller = 'sales_creditmemo';
        $this->_headerText = __('Credit Memos Archive');
    }
}
