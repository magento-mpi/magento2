<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Checkout_Block_Link extends Mage_Page_Block_Link
{
    /**
     * @return string
     */
    public function getHref()
    {
        return $this->getUrl('checkout', array('_secure' => true));
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->helper('Mage_Checkout_Helper_Data')->canOnepageCheckout()
            || !$this->helper('Mage_Core_Helper_Data')->isModuleOutputEnabled('Mage_Checkout')
        ) {
            return '';
        }
        return parent::_toHtml();
    }
}
