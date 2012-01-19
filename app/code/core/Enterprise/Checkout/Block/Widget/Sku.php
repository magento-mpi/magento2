<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Order by SKU Widget Block
 *
 * @category   Enterprise
 * @package    Enterprise_Checkout
 */
class Enterprise_Checkout_Block_Widget_Sku
    extends Mage_Core_Block_Template
    implements Mage_Widget_Block_Interface
{
    /**
     * Retrieve form action URL
     *
     * @return string
     */
    public function getFormAction()
    {
        return $this->getUrl('checkout/cart/advancedAdd');
    }

    /**
     * Get link to "Order by SKU" on customer's account page
     *
     * @return string
     */
    public function getLink()
    {
        $data = $this->getData();
        if (empty($data['link_display']) || empty($data['link_text'])) {
            return '';
        }

        /** @var $helper Enterprise_Checkout_Helper_Data */
        $helper = Mage::helper('Enterprise_Checkout_Helper_Data');
        if (!$helper->isSkuEnabled() || !$helper->isSkuApplied()) {
            return '';
        }

        return '<a href="' . $this->escapeHtml($this->getUrl('enterprise_checkout/sku')) . '">'
            . $this->escapeHtml($data['link_text']) . '</a>';
    }
}
