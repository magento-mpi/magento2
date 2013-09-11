<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Rma\Block\Adminhtml\Product\Bundle;

class Js extends \Magento\Adminhtml\Block\Template
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
            'order_id' => \Mage::registry('current_order')->getId()
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
