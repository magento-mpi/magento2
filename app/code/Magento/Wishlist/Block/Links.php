<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Wishlist
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Links block
 *
 * @category    Magento
 * @package     Magento_Wishlist
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Wishlist_Block_Links extends Magento_Page_Block_Template_Links_Block
{
    /**
     * Position in link list
     * @var int
     */
    protected $_position = 30;

    /**
     * Wishlist data
     *
     * @var Magento_Wishlist_Helper_Data
     */
    protected $_wishlistData = null;

    /**
     * @param Magento_Wishlist_Helper_Data $wishlistData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Wishlist_Helper_Data $wishlistData,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_wishlistData = $wishlistData;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->_wishlistData->isAllow()) {
            $text = $this->_createLabel($this->_getItemCount());
            $this->_label = $text;
            $this->_title = $text;
            $this->_url = $this->getUrl('wishlist');
            return parent::_toHtml();
        }
        return '';
    }

    /**
     * Define label, title and url for wishlist link
     *
     * @deprecated after 1.6.2.0
     */
    public function initLinkProperties()
    {
        $text = $this->_createLabel($this->_getItemCount());
        $this->_label = $text;
        $this->_title = $text;
        $this->_url = $this->getUrl('wishlist');
    }

    /**
     * Count items in wishlist
     *
     * @return int
     */
    protected function _getItemCount()
    {
        return $this->_wishlistData->getItemCount();
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
            return __('My Wish List (%1 items)', $count);
        } else if ($count == 1) {
            return __('My Wish List (%1 item)', $count);
        } else {
            return __('My Wish List');
        }
    }
}
