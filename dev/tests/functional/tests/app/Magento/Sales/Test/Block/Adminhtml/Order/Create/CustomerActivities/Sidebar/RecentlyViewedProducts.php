<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Block\Adminhtml\Order\Create\CustomerActivities\Sidebar;

use Magento\Sales\Test\Block\Adminhtml\Order\Create\CustomerActivities\Sidebar;
use Magento\Widget\Test\Fixture\Widget;
use Mtf\Client\Element\Locator;

/**
 * Class RecentlyViewedProducts
 * Recently viewed products block
 */
class RecentlyViewedProducts extends Sidebar
{
    /**
     * Recently Viewed Products selectors
     *
     * @var string
     */
    protected $recentlyViewedProducts = './/tbody/tr/td[1]';

    /**
     * Get products from Recently Viewed block
     *
     * @return array
     */
    public function getProducts()
    {
        $products = [];
        $productNames = $this->_rootElement->find($this->recentlyViewedProducts, Locator::SELECTOR_XPATH)
            ->getElements();
        foreach ($productNames as $productName) {
            $products[] = $productName->getText();
        }
        return $products;
    }
}
