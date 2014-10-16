<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\MultipleWishlist\Test\Block\Adminhtml\Sales\Order\Create\Sidebar;

use Mtf\Client\Element\Locator;
use Magento\Sales\Test\Block\Adminhtml\Order\Create\CustomerActivities\Sidebar;

/**
 * Class Wishlist
 * Wish list block on backend
 */
class Wishlist extends Sidebar
{
    /**
     * Wish list locator
     *
     * @var string
     */
    protected $wishlist = '.sidebar-selector';

    /**
     * Wish list items locator
     *
     * @var string
     */
    protected $wishlistItems = '#sidebar_data_wishlist';

    /**
     * Select wish list in Wish list dropdown
     *
     * @param string $name
     * @return bool
     */
    public function selectWishlist($name)
    {
        $this->_rootElement->find($this->wishlist, Locator::SELECTOR_CSS, 'select')->setValue($name);
    }

    /**
     * Get last ordered items block
     *
     * @return \Mtf\Block\BlockInterface
     */
    public function getWishlistItemsBlock()
    {
        return $this->blockFactory->create(
            'Magento\MultipleWishlist\Test\Block\Adminhtml\Sales\Order\Create\Sidebar\Wishlist\Items',
            ['element' => $this->_rootElement->find($this->wishlistItems)]
        );
    }
}
