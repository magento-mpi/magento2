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

class Link extends \Magento\View\Element\Html\Link implements \Magento\View\Block\IdentityInterface
{
    /**
     * Template name
     *
     * @var string
     */
    protected $_template = 'Magento_Wishlist::link.phtml';

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
        return __('My Wish List');
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->getLabel();
    }

    /**
     * @return string
     */
    public function getCounter()
    {
        return $this->_createCounter($this->_getItemCount());
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
    protected function _createCounter($count)
    {
        if ($count > 1) {
            return __('%1 items', $count);
        } else if ($count == 1) {
            return __('1 item');
        } else {
            return;
        }
    }

    /**
     * Retrieve block cache tags
     *
     * @return array
     */
    public function getIdentities()
    {
        /** @var $wishlist \Magento\Wishlist\Model\Wishlist */
        $wishlist = $this->_wishlistHelper->getWishlist();
        $identities = $wishlist->getIdentities();
        foreach ($wishlist->getItemCollection() as $item) {
            /** @var $item \Magento\Wishlist\Model\Item */
            $identities = array_merge($identities, $item->getProduct()->getIdentities());
        }
        return $identities;
    }
}
