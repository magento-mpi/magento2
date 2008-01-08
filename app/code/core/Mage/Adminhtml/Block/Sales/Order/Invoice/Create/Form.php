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
 * Adminhtml invoice create form
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Michael Bessolov <michael@varien.com>
 */

class Mage_Adminhtml_Block_Sales_Order_Invoice_Create_Form extends Mage_Adminhtml_Block_Widget_Form
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('invoice_form');
        $this->setTemplate('sales/order/invoice/create/form.phtml');
    }

    public function getOrder()
    {
        return Mage::registry('current_order');
    }

    protected function _prepareLayout()
    {
        $this->setChild(
            'items',
            $this->getLayout()->createBlock('adminhtml/sales_order_invoice_create_items')
        );
        return parent::_prepareLayout();
    }

    public function getItemsHtml()
    {
        return $this->getChildHtml('items');
    }

    public function getSaveUrl()
    {
        return Mage::getUrl('*/*/save', array('order_id' => $this->getOrder()->getId()));
    }
}