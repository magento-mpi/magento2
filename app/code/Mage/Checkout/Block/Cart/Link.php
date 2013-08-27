<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Checkout_Block_Cart_Link extends Mage_Page_Block_Link
{
    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->_createLabel($this->_getItemCount());
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->_createLabel($this->_getItemCount());
    }

    /**
     * @return string
     */
    public function getHref()
    {
        return $this->getUrl('checkout/cart');
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->helper('Mage_Core_Helper_Data')->isModuleOutputEnabled('Mage_Checkout')) {
            return '';
        }
        return parent::_toHtml();
    }

    /**
     * Count items in cart
     *
     * @return int
     */
    protected function _getItemCount()
    {
        $count = $this->getSummaryQty();
        return $count ? $count : $this->helper('Mage_Checkout_Helper_Cart')->getSummaryCount();
    }

    /**
     * Create link label based on cart item quantity
     *
     * @param int $count
     * @return string
     */
    protected function _createLabel($count)
    {
        if ($count == 1) {
            return $this->__('My Cart (%s item)', $count);
        } elseif ($count > 0) {
            return $this->__('My Cart (%s items)', $count);
        } else {
            return $this->__('My Cart');
        }
    }
}
