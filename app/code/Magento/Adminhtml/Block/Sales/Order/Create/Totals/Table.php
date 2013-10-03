<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml sales order create totals table block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Adminhtml\Block\Sales\Order\Create\Totals;

class Table extends \Magento\Adminhtml\Block\Template
{

    protected $_websiteCollection = null;

    protected function _construct()
    {
        parent::_construct();
        $this->setId('sales_order_create_totals_table');
    }

}
