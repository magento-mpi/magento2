<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * "My Wish List" link
 */
namespace Magento\Wishlist\Block;

class Link extends \Magento\View\Element\Html\Link
{
    /**
     * @var \Magento\Wishlist\Helper\Data
     */
    protected $_wishlistHelper;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Wishlist\Helper\Data $wishlistHelper
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Wishlist\Helper\Data $wishlistHelper,
        array $data = array()
    ) {
        $this->_wishlistHelper = $wishlistHelper;
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->_wishlistHelper->isAllow()) {
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
        return $this->_wishlistHelper->getItemCount();
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
