<?php
/**
 * Adminhtml sales order create sidebar
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Michael Bessolov <michael@varien.com>
 */

class Mage_Adminhtml_Block_Sales_Order_Create_Form extends Mage_Adminhtml_Block_Sales_Order_Create_Abstract
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('sales_order_create_form');
        $this->setTemplate('sales/order/create/form.phtml');
    }

    protected function _initChildren()
    {
        $this->setChild('before', $this->getLayout()->createBlock('core/template')->setTemplate('sales/order/create/form/before.phtml'));
        $childNames = array();
        if (! $this->getCustomerId()) {
            $childNames[] = 'customer';
        } elseif (! $this->getStoreid()) {
            $childNames[] = 'store';
        } else {
            $childNames[] = 'shipping_address';
            $childNames[] = 'billing_address';
            $childNames[] = 'shipping_method';
            $childNames[] = 'billing_method';
            $childNames[] = 'coupons';
            $childNames[] = 'newsletter';
            $childNames[] = 'items';
            // $childNames[] = 'search';
            $childNames[] = 'totals';
        }

        foreach ($childNames as  $name) {
            $this->setChild($name, $this->getLayout()->createBlock('adminhtml/sales_order_create_' . $name));
        }
        $this->setChild('after', $this->getLayout()->createBlock('core/template')->setTemplate('sales/order/create/form/after.phtml'));
        return parent::_initChildren();
    }

    public function getSaveUrl()
    {
        return Mage::getUrl('*/*/save');
    }

}
