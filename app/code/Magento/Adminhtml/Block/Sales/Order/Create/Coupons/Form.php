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
 * Adminhtml sales order create coupons form block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Adminhtml\Block\Sales\Order\Create\Coupons;

class Form extends \Magento\Adminhtml\Block\Sales\Order\Create\AbstractCreate
{

    protected function _construct()
    {
        parent::_construct();
        $this->setId('sales_order_create_coupons_form');
    }

    public function getCouponCode()
    {
        return $this->getParentBlock()->getQuote()->getCouponCode();
    }

}
