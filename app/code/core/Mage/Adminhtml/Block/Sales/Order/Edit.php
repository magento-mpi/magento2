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
 * Adminhtml sales order edit
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Michael Bessolov <michael@varien.com>
 */

class Mage_Adminhtml_Block_Sales_Order_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{

    public function __construct()
    {
        $this->_objectId = 'order_id';
        $this->_controller = 'sales_order';

        parent::__construct();

        $this->_updateButton('save', 'label', Mage::helper('sales')->__('Save Order'));
        $this->_removeButton('delete');
    }

    public function getHeaderText()
    {
        if (Mage::registry('sales_order')->getId()) { // TOCHECK
            return Mage::helper('sales')->__('Edit Order #%s', Mage::registry('sales_order')->getRealOrderId());
        }
        else {
            return Mage::helper('sales')->__('New Order');
        }
    }

    public function getBackUrl()
    {
        return Mage::getUrl('*/sales_order/view', array('order_id' => Mage::registry('sales_order')->getId()));
    }

}