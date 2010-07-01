<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Enterprise
 * @package     Enterprise_SalesPool
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Pool orders grid container
 *
 */
class Enterprise_SalesPool_Block_Adminhtml_Sales_Pool_Order extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    protected function _prepareLayout()
    {
        $this->_removeButton('add');
        $this->_controller = 'sales_order';

        if (Mage::getSingleton('admin/session')->isAllowed('sales/pool/order/flush')) {
            $this->_addButton('flush', array(
                'label' => Mage::helper('enterprise_salespool')->__('Process All Orders'),
                'class' => 'delete',
                'onclick' => 'setLocation(\'' . $this->getUrl('*/*/flushAll') . '\')'
            ));
        }

        $this->_headerText = Mage::helper('enterprise_salespool')->__('Orders Queue');

        foreach ($this->_buttons as $level => $buttons) {
            foreach ($buttons as $id => $data) {
                $childId = $this->_prepareButtonBlockId($id);
                $this->_addButtonChildBlock($childId);
            }
        }
        return $this;
    }
}
