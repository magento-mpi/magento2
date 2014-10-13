<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Block\Category;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;
use Magento\Widget\Test\Fixture\Widget;

/**
 * Class View
 * Category view block on the category page
 */
class View extends Block
{
    /**
     * Recently Viewed Products selectors
     *
     * @var string
     */
    protected $recentlyViewedProducts = './/*[contains(@class,"widget")]//strong[@class="product name"]';

    /**
     * Description CSS selector
     *
     * @var string
     */
    protected $description = '.category-description';

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->_rootElement->find($this->description)->getText();
    }

    /**
     * Get Category Content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->_rootElement->getText();
    }

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
