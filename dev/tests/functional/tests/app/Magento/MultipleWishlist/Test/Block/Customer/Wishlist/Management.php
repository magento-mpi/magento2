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
use Magento\Catalog\Test\Fixture\CatalogProductSimple;

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
    protected $addWishlist = '#wishlist-create-button';

    /**
     * Wish list select selector
     *
     * @var string
     */
    protected $wishlistSelect = '#wishlists-select span';

    /**
     * Options multiple wish list
     *
     * @var string
     */
    protected $wishlistOptions = '.wishlist-select-items';

    /**
     * Item wish list
     *
     * @var string
     */
    protected $wishlistItem = './/a[.="%s"]';

    /**
     * Notice type selector
     *
     * @var string
     */
    protected $noticeType = '.wishlist-notice';

    /**
     * Button 'Delete Wishlist' css selector
     *
     * @var string
     */
    protected $removeButton = 'button.remove';

    /**
     * Button 'Edit' css selector
     *
     * @var string
     */
    protected $editButton = '.action.edit';

    /**
     * Create new wish list
     *
     * @return void
     */
    public function clickCreateNewWishlist()
    {
        $this->_rootElement->find($this->addWishlist)->click();
    }

    /**
     * Selected item wish list by name
     *
     * @param string $wishlistName
     * @return void
     */
    public function selectedWishlistByName($wishlistName)
    {
        $this->_rootElement->find(sprintf($this->wishlistItem, $wishlistName), Locator::SELECTOR_XPATH)->click();
    }

    /**
     * Is visible wish list by name
     *
     * @param string $wishlistName
     * @return bool
     */
    public function isWishlistVisible($wishlistName)
    {
        return $this->_rootElement->find(sprintf($this->wishlistItem, $wishlistName), Locator::SELECTOR_XPATH)
            ->isVisible();
    }

    /**
     * Notice type visibility
     *
     * @param string $type
     * @return bool
     */
    public function isNoticeTypeVisible($type)
    {
        return $this->_rootElement->find($this->noticeType . '.' . $type)->isVisible();
    }

    /**
     * Delete wish list
     *
     * @return void
     */
    public function removeWishlist()
    {
        $this->_rootElement->find($this->removeButton)->click();
        $this->_rootElement->acceptAlert();
    }

    /**
     * Remove button is visible
     *
     * @return bool
     */
    public function isRemoveButtonVisible()
    {
        return $this->_rootElement->find($this->removeButton)->isVisible();
    }

    /**
     * Click Edit wish list button
     *
     * @return void
     */
    public function editWishlist()
    {
        $this->_rootElement->find($this->editButton)->click();
    }
}
