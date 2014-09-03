<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Reports\Test\Block\Adminhtml\Review\Products\Viewed;

use Magento\Backend\Test\Block\Widget\Grid;

/**
 * Class ProductGrid
 * Product Views Report
 */
class ProductGrid extends Grid
{
    /**
     * Product in grid locator
     *
     * @var string
     */
    protected $product = './/*[contains(.,"%s") and *[contains(@class,"price") and contains(.,"%d")]]';

    /**
     * Count product views
     *
     * @var string
     */
    protected $productView = '/*[contains(@class,"qty")]';

    /**
     * Get total Results from New Accounts Report grid
     *
     * @param array $products
     * @return array
     */
    public function getViewsResults(array $products)
    {
        $views = [];
        foreach ($products as $product) {
            $views[] = $this->_rootElement
                ->find(sprintf($this->product . $this->productView, $product->getName(), $product->getPrice()))
                ->getText();
        }
        return $views;
    }
}
