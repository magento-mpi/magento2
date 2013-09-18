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
     * @var \Magento\MultipleWishlist\Helper\Data
     */
    protected $_wishlistData = null;

    /**
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\MultipleWishlist\Helper\Data $wishlistData
     * @param \Magento\Core\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\MultipleWishlist\Helper\Data $wishlistData,
        \Magento\Core\Block\Template\Context $context,
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
