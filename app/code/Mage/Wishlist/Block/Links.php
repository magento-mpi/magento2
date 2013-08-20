<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Wishlist
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Links block
 *
 * @category    Mage
 * @package     Mage_Wishlist
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Wishlist_Block_Links extends Mage_Page_Block_Link
{
    /**
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->helper('Mage_Wishlist_Helper_Data')->isAllow()) {
            return parent::_toHtml();
        }
        return '';
    }

    /**
     * @return string
     */
    public function getHref()
    {
        return $this->getUrl('wishlist');
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
    public function getTitle()
    {
        return $this->_createLabel($this->_getItemCount());
    }

    /**
     * Count items in wishlist
     *
     * @return int
     */
    protected function _getItemCount()
    {
        return $this->helper('Mage_Wishlist_Helper_Data')->getItemCount();
    }

    /**
     * Create button label based on wishlist item quantity
     *
     * @param int $count
     * @return string
     */
    protected function _createLabel($count)
    {
        if ($count > 1) {
            return $this->__('My Wish List (%d items)', $count);
        } else if ($count == 1) {
            return $this->__('My Wish List (%d item)', $count);
        } else {
            return $this->__('My Wish List');
        }
    }
}
