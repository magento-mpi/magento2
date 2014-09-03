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
     * Total results locator
     *
     * @var string
     */
    protected $totalResults = './/tr[td[contains(.,"%s")] and td[contains(.,"%d")]]/td[contains(@class,"col-qty")]';

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
                ->find(sprintf($this->totalResults, $product->getName(), $product->getPrice()))->getText();
        }
        return $views;
    }
}
