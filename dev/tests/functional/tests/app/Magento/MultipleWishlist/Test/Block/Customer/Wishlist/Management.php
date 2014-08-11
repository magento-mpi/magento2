<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\MultipleWishlist\Test\Block\Customer\Wishlist;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;

/**
 * Class Management
 * Management wish list block on 'My Wish List' page
 */
class Management extends Block
{
    /**
     * Button "Create New Wish List" selector
     *
     * @var string
     */
    protected $addWishlist = '.action.add.wishlist';

    /**
     * Wish list select selector
     *
     * @var string
     */
    protected $wishlistSelect = '.actions.dropdown.wishlist span';

    /**
     * Options multiple wish list
     *
     * @var string
     */
    protected $wishlistOptions = '.items.dropdown';

    /**
     * Item wish list
     *
     * @var string
     */
    protected $wishlistItem = '//a[.="%s"]';

    /**
     * Notice message selector
     *
     * @var string
     */
    protected $noticeMessage = '.message.notice';

    /**
     * Create new wish list
     *
     * @return void
     */
    public function clickCreateWishlist()
    {
        $this->_rootElement->find($this->addWishlist)->click();
    }

    /**
     * Get wish lists
     *
     * @return array
     */
    public function getWishlists()
    {
        $this->clickWishlistSelect();
        $options = trim($this->_rootElement->find($this->wishlistOptions)->getText());
        $options = explode("\n", $options);
        if (in_array('Create New Wish List', $options)) {
            array_pop($options);
        }
        return $options;
    }

    /**
     * Click wish list select
     *
     * @return void
     */
    protected function clickWishlistSelect()
    {
        $this->_rootElement->find($this->wishlistSelect)->click();
    }

    /**
     * Selected item wish list by name
     *
     * @param string $wishlistName
     * @return void
     */
    public function selectedWishlistByName($wishlistName)
    {
        $this->clickWishlistSelect();
        $this->_rootElement->find(sprintf($this->wishlistItem, $wishlistName), Locator::SELECTOR_XPATH)->click();
    }

    /**
     * Is visible wish list by name
     *
     * @param string $wishlistName
     * @return bool
     */
    public function wishlistIsVisible($wishlistName)
    {
        $isVisible = in_array($wishlistName, $this->getWishlists());
        if ($isVisible != false) {
            return true;
        }
        return false;
    }

    /**
     * Get notice message
     *
     * @return string
     */
    public function getNoticeMessage()
    {
        return trim($this->_rootElement->find($this->noticeMessage)->getText());
    }
}
