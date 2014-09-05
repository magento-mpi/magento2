<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Block\Adminhtml\Order\Create;

use Mtf\Block\Block;

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
    protected $updateChanges = '.action-.scalable';

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
     * Get last ordered items block
     *
     * @return \Magento\Sales\Test\Block\Adminhtml\Order\Create\CustomerActivities\Sidebar\LastOrderedItems
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
     * @return \Magento\Sales\Test\Block\Adminhtml\Order\Create\CustomerActivities\Sidebar\ProductsInComparison
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
     * @return \Magento\Sales\Test\Block\Adminhtml\Order\Create\CustomerActivities\Sidebar\RecentlyComparedProducts
     */
    public function getRecentlyComparedProductsBlock()
    {
        return $this->blockFactory->create(
            'Magento\Sales\Test\Block\Adminhtml\Order\Create\CustomerActivities\Sidebar\RecentlyComparedProducts',
            ['element' => $this->_rootElement->find($this->recentlyComparedSidebar)]
        );
    }

    /**
     * Click 'Update Changes' button
     *
     * @return void
     */
    public function updateChanges()
    {
        $this->_rootElement->find($this->updateChanges)->click();
    }
}
