<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Block\Adminhtml\Order\Create;

use Mtf\Block\Block;
use Magento\Sales\Test\Block\Adminhtml\Order\Create\CustomerActivities\Sidebar\LastOrderedItems;
use Magento\Sales\Test\Block\Adminhtml\Order\Create\CustomerActivities\Sidebar\ProductsInComparison;
use Magento\Sales\Test\Block\Adminhtml\Order\Create\CustomerActivities\Sidebar\RecentlyComparedProducts;
use Magento\Sales\Test\Block\Adminhtml\Order\Create\CustomerActivities\Sidebar\RecentlyViewedProducts;
use Magento\Sales\Test\Block\Adminhtml\Order\Create\CustomerActivities\Sidebar\ShoppingCartItems;
use Mtf\Client\Element\Locator;

/**
 * Class CustomerActivities
 * Customer's Activities block
 */
class CustomerActivities extends Block
{
    /**
     * 'Update Changes' button
     *
     * @var string
     */
    protected $updateChanges = '.actions .action-.scalable';

    /**
     * Order sidebar reorder css selector
     *
     * @var string
     */
    protected $reorderSidebar = '#order-sidebar_reorder';

    /**
     * Order sidebar compared css selector
     *
     * @var string
     */
    protected $comparedSidebar = '#order-sidebar_compared';

    /**
     * Order sidebar compared css selector
     *
     * @var string
     */
    protected $recentlyComparedSidebar = '#order-sidebar_pcompared';

    /**
     * Order sidebar viewed css selector
     *
     * @var string
     */
    protected $recentlyViewedSidebar = '#order-sidebar_pviewed';

    /**
     * Shopping cart sidebar selector
     * Shopping cart sidebar selector
     *
     * @var string
     */
    protected $shoppingCartSidebar = '#order-sidebar_cart';

    // @codingStandardsIgnoreStart
    /**
     * Last sidebar block selector
     *
     * @var string
     */
    protected $lastSidebar = '//*[@class="create-order-sidebar-container"]/div[div[@class="create-order-sidebar-block"]][last()]';
    // @codingStandardsIgnoreEnd

    /**
     * Get last ordered items block
     *
     * @return LastOrderedItems
     */
    public function getLastOrderedItemsBlock()
    {
        return $this->blockFactory->create(
            'Magento\Sales\Test\Block\Adminhtml\Order\Create\CustomerActivities\Sidebar\LastOrderedItems',
            ['element' => $this->_rootElement->find($this->reorderSidebar)]
        );
    }

    /**
     * Get products in comparison block
     *
     * @return ProductsInComparison
     */
    public function getProductsInComparisonBlock()
    {
        return $this->blockFactory->create(
            'Magento\Sales\Test\Block\Adminhtml\Order\Create\CustomerActivities\Sidebar\ProductsInComparison',
            ['element' => $this->_rootElement->find($this->comparedSidebar)]
        );
    }

    /**
     * Get products in comparison block
     *
     * @return RecentlyComparedProducts
     */
    public function getRecentlyComparedProductsBlock()
    {
        return $this->blockFactory->create(
            'Magento\Sales\Test\Block\Adminhtml\Order\Create\CustomerActivities\Sidebar\RecentlyComparedProducts',
            ['element' => $this->_rootElement->find($this->recentlyComparedSidebar)]
        );
    }

    /**
     * Get products in view block
     *
     * @return RecentlyViewedProducts
     */
    public function getRecentlyViewedProductsBlock()
    {
        return $this->blockFactory->create(
            'Magento\Sales\Test\Block\Adminhtml\Order\Create\CustomerActivities\Sidebar\RecentlyViewedProducts',
            ['element' => $this->_rootElement->find($this->recentlyViewedSidebar)]
        );
    }

    /**
     * Get shopping Cart items block
     *
     * @return ShoppingCartItems
     */
    public function getShoppingCartItemsBlock()
    {
        return $this->blockFactory->create(
            'Magento\Sales\Test\Block\Adminhtml\Order\Create\CustomerActivities\Sidebar\ShoppingCartItems',
            ['element' => $this->_rootElement->find($this->shoppingCartSidebar)]
        );
    }

    /**
     * Click 'Update Changes' button
     *
     * @return void
     */
    public function updateChanges()
    {
        $this->_rootElement->find($this->lastSidebar, Locator::SELECTOR_XPATH)->click();
        $this->_rootElement->find($this->updateChanges)->click();
    }
}
