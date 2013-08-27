<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_Rma_Block_Adminhtml_Product_Bundle_Js extends Magento_Adminhtml_Block_Template
{
    /**
     * Get url for Bundle AJAX Action
     *
     * @return string
     */
    public function getLoadBundleUrl()
    {
        return $this->getUrl('*/*/showBundleItems');
    }

    /**
     * Get url for Details AJAX Action
     *
     * @return string
     */
    public function getLoadAttributesUrl()
    {
        return $this->getUrl('*/*/loadNewAttributes', array(
            'order_id' => Mage::registry('current_order')->getId()
        ));
    }

    /**
     * Get load order id
     *
     * @return int
     */
    public function getLoadOrderId()
    {
        return $this->getRequest()->getParam('order_id');
    }
}
