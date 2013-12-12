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
 * Adminhtml sales order create newsletter form block
 *
 * @category   Magento
 * @package    Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Sales\Block\Adminhtml\Order\Create\Newsletter;

class Form extends \Magento\Backend\Block\Widget
{

    protected function _construct()
    {
        parent::_construct();
        $this->setId('sales_order_create_newsletter_form');
    }

}
