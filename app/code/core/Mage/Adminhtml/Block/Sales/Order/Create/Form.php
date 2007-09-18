<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml sales order create sidebar
 *
 * @category   Mage
 * @package    Mage_Adminhtml
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

    protected function _prepareLayout()
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
        return parent::_prepareLayout();
    }

    public function getSaveUrl()
    {
        return Mage::getUrl('*/*/save');
    }

}
