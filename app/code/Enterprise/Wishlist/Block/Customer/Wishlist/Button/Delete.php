<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Wishlist
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Wishlist delete button
 *
 * @category    Enterprise
 * @package     Enterprise_Wishlist
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Wishlist_Block_Customer_Wishlist_Button_Delete extends Magento_Wishlist_Block_Abstract
{
    /**
     * Wishlist data
     *
     * @var Enterprise_Wishlist_Helper_Data
     */
    protected $_wishlistData = null;

    /**
     * @param Enterprise_Wishlist_Helper_Data $wishlistData
     * @param Magento_Wishlist_Helper_Data $wishlistData
     * @param Magento_Tax_Helper_Data $taxData
     * @param Magento_Catalog_Helper_Data $catalogData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Enterprise_Wishlist_Helper_Data $wishlistData,
        Magento_Wishlist_Helper_Data $wishlistData,
        Magento_Tax_Helper_Data $taxData,
        Magento_Catalog_Helper_Data $catalogData,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_wishlistData = $wishlistData;
        parent::__construct($wishlistData, $taxData, $catalogData, $coreData, $context, $data);
    }

    /**
     * Build block html
     *
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->_wishlistData->isMultipleEnabled() && $this->isWishlistDeleteable()) {
            return parent::_toHtml();
        }
        return '';
    }

    /**
     * Check whether current wishlist can be deleted
     *
     * @return bool
     */
    protected function isWishlistDeleteable()
    {
        return !$this->_wishlistData->isWishlistDefault($this->getWishlistInstance());
    }

    /**
     * Build wishlist deletion url
     *
     * @return string
     */
    public function getDeleteUrl()
    {
        return $this->getUrl('wishlist/index/deletewishlist', array('wishlist_id' => '%item%'));
    }

    /**
     * Retrieve url to redirect customer to after wishlist is deleted
     *
     * @return string
     */
    public function getRedirectUrl()
    {
        return $this->getUrl('wishlist/index/index');
    }
}
