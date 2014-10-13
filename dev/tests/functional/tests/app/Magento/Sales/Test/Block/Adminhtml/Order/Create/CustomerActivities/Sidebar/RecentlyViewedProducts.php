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
    protected $recentlyViewedProducts = './/*[contains(@class,"create-order-sidebar-block")]//tbody/tr/td[1]';

    /**
     * Check that block Recently Viewed contains product
     *
     * @param Widget $widget
     * @return bool
     */
    public function checkProductInRecentlyViewedBlock(Widget $widget)
    {
        $products = [];
        $productNames = $this->_rootElement->find($this->recentlyViewedProducts, Locator::SELECTOR_XPATH)
            ->getElements();
        foreach ($productNames as $productName) {
            $products[] = $productName->getText();
        }
        return in_array($widget->getWidgetOptions()[0]['entities'][0]->getName(), $products);
    }
}
