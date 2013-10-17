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
 * Adminhtml sales order create shipping method block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Sales\Block\Adminhtml\Order\Create\Shipping;

class Method extends \Magento\Sales\Block\Adminhtml\Order\Create\AbstractCreate
{

    protected function _construct()
    {
        parent::_construct();
        $this->setId('sales_order_create_shipping_method');
    }

    public function getHeaderText()
    {
        return __('Shipping Method');
    }

    public function getHeaderCssClass()
    {
        return 'head-shipping-method';
    }
}
