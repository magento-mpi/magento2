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
 * @package     Enterprise_Rma
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

class Enterprise_Rma_Block_Return_Create extends Enterprise_Rma_Block_Form
{
    public function _construct()
    {
        $order = Mage::registry('current_order');
        $this->setOrder($order);

        $items = Mage::helper('enterprise_rma')->getOrderItems($order);
        $this->setItems($items);

        $session = Mage::getSingleton('core/session');
        $formData = $session->getRmaFormData(true);
        if (!empty($formData)) {
            $data = new Varien_Object();
            $data->addData($formData);
            $this->setFormData($data);
        }
        $errorKeys = $session->getRmaErrorKeys(true);
        if (!empty($errorKeys)) {
            $data = new Varien_Object();
            $data->addData($errorKeys);
            $this->setErrorKeys($data);
        }
    }

    /**
     * Retrieves item qty available for return
     *
     * @param  $item | Mage_Sales_Model_Order_Item
     * @return int
     */
    public function getAvailableQty($item)
    {
        $return = $item->getAvailableQty();
        if (!$item->getIsQtyDecimal()) {
            $return = intval($return);
        }
        return $return;
    }



    public function getBackUrl()
    {
        return Mage::getUrl('sales/order/history');
    }


    /**
     * Prepare rma item attributes
     *
     * @return array
     */
    public function getAttributes()
    {
        /* @var $itemModel  */
        $itemModel = Mage::getModel('enterprise_rma/item');

        /* @var $itemForm Enterprise_Rma_Model_Item_Form */
        $itemForm   = Mage::getModel('enterprise_rma/item_form');
        $itemForm->setFormCode('default')
            ->setStore($this->getStore())
            ->setEntity($itemModel);

        // prepare item attributes to show
        $attributes     = array();

        // add system required attributes
        foreach ($itemForm->getSystemAttributes() as $attribute) {
            /* @var $attribute Enterprise_Rma_Model_Item_Attribute */
            if ($attribute->getIsVisible()) {
                $attributes[$attribute->getAttributeCode()] = $attribute;
            }
        }

        // add user defined attributes
        foreach ($itemForm->getUserAttributes() as $attribute) {
            /* @var $attribute Enterprise_Rma_Model_Item_Attribute */
            if ($attribute->getIsVisible()) {
                $attributes[$attribute->getAttributeCode()] = $attribute;
            }
        }

        return $attributes;
    }

    /**
     * Retrieves Contact Email Address on error
     *
     * @return string
     */
    public function getContactEmail()
    {
        $data   = $this->getFormData();
        $email  = '';

        if ($data) {
            $email = $this->escapeHtml($data->getCustomerCustomEmail());
        }
        return $email;
    }
}