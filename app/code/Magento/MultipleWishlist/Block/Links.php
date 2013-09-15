<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_MultipleWishlist
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Links block
 *
 * @category    Magento
 * @package     Magento_MultipleWishlist
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\MultipleWishlist\Block;

class Links extends \Magento\Wishlist\Block\Links
{
    /**
     * Wishlist data
     *
     * @var Magento_MultipleWishlist_Helper_Data
     */
    protected $_wishlistData = null;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_MultipleWishlist_Helper_Data $wishlistData
     * @param Magento_Core_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_MultipleWishlist_Helper_Data $wishlistData,
        Magento_Core_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_wishlistData = $wishlistData;
        parent::__construct($coreData, $context, $data);
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
     * Create Button label
     *
     * @param int $count
     * @return string
     */
    protected function _createLabel($count)
    {
        if ($this->_wishlistData->isMultipleEnabled()) {
            if ($count > 1) {
                return __('My Wish Lists (%1 items)', $count);
            } else if ($count == 1) {
                return __('My Wish Lists (%1 item)', $count);
            } else {
                return __('My Wish Lists');
            }
        } else {
            return parent::_createLabel($count);
        }
    }
}
