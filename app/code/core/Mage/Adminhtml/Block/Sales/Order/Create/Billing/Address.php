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
 * Adminhtml sales order create billing address block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Michael Bessolov <michael@varien.com>
 */

class Mage_Adminhtml_Block_Sales_Order_Create_Billing_Address extends Mage_Adminhtml_Block_Sales_Order_Create_Form_Address
{
    protected function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

    public function getHeaderText()
    {
        return __('Billing Address');
    }

    public function getHeaderCssClass()
    {
        return 'head-billing-address';
    }
    
    protected function _prepareForm()
    {
        if (!$this->_form) {
        	parent::_prepareForm();
            $this->_form->addFieldNameSuffix('order[billing_address]');
            $this->_form->setHtmlIdPrefix('order:billing_addres_');
        }
        return $this;
    }
    
    public function getFormValues()
    {
        return $this->getCreateOrderModel()->getBillingAddress()->getData();
    }

    
    public function getAddressId()
    {
        return $this->getCreateOrderModel()->getBillingAddress()->getCustomerAddressId();
    }
}
