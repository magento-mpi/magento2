<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml sales order create totals table block
 *
 * @category   Magento
 * @package    Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Sales\Block\Adminhtml\Order\Create\Totals;

class Table extends \Magento\Backend\Block\Template
{

    protected $_websiteCollection = null;

    protected function _construct()
    {
        parent::_construct();
        $this->setId('sales_order_create_totals_table');
    }

}
