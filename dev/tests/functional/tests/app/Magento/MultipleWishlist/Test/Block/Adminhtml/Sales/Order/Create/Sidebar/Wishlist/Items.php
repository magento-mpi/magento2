<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\MultipleWishlist\Test\Block\Adminhtml\Sales\Order\Create\Sidebar\Wishlist;

use Mtf\Client\Element\Locator;
use Mtf\Fixture\InjectableFixture;
use Magento\GroupedProduct\Test\Fixture\GroupedProductInjectable;
use Magento\Sales\Test\Block\Adminhtml\Order\Create\CustomerActivities\Sidebar;

/**
 * Class Items
 * Wish list items block on backend
 */
class Items extends Sidebar
{
    /**
     * Locator for wish list item name
     *
     * @var string
     */
    protected $itemName = '//tr/td[contains(.,"%s")]';

    // @codingStandardsIgnoreStart
    /**
     * Locator for 'Add To Order' checkbox
     *
     * @var string
     */
    protected $addToOrder = '//tr[td[contains(.,"%s")]][td[contains(.,"%d")]][td/span[contains(., "%d")]]//input[contains(@name,"[add_wishlist_item]")]';
    // @codingStandardsIgnoreEnd

    /**
     * Locator for 'Add to order' link for Grouped product
     *
     * @var string
     */
    protected $addToOrderGrouped = '//tr[td[contains(.,"%s")]]//td/a/img';

    /**
     * Locator for submit button
     *
     * @var string
     */
    protected $submit = '//ancestor::body//button[@data-ui-id="order-content-submit-order-top-button-button"]';

    /**
     * Select item to add to order
     *
     * @param InjectableFixture $product
     * @param string $qty
     * @return void
     */
    public function selectItemToAddToOrder(InjectableFixture $product, $qty)
    {
        $target = $this->_rootElement->find($this->submit, Locator::SELECTOR_XPATH);
        $this->_rootElement->find(
            sprintf($this->itemName, $product->getName()),
            Locator::SELECTOR_XPATH
        )->dragAndDrop($target);

        if ($product instanceof GroupedProductInjectable) {
            $this->_rootElement->find(
                sprintf($this->addToOrderGrouped, $product->getName()),
                Locator::SELECTOR_XPATH
            )->click();
        } else {
            $this->_rootElement->find(
                sprintf($this->addToOrder, $product->getName(), $qty, $product->getCheckoutData()['cartItem']['price']),
                Locator::SELECTOR_XPATH,
                'checkbox'
            )->setValue('Yes');
        }
    }
}
