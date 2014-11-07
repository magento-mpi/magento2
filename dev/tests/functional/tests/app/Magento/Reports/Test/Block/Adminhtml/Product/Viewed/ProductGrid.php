<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Reports\Test\Block\Adminhtml\Product\Viewed;

use Mtf\Client\Element\Locator;
use Magento\Backend\Test\Block\Widget\Grid;

/**
 * Product Views Report.
 */
class ProductGrid extends Grid
{
    /**
     * Product in grid locator.
     *
     * @var string
     */
    protected $product = './/*[contains(.,"%s") and *[contains(@class,"price") and contains(.,"%d")]]';

    /**
     * Count product views.
     *
     * @var string
     */
    protected $productView = '/*[contains(@class,"qty")]';

    /**
     * Get views Results from Products Report grid.
     *
     * @param array $products
     * @param string $date
     * @return array
     */
    public function getViewsResults(array $products, $date = '')
    {
        $views = [];
        $date = date($date);
        if ($date) {
            $text = $this->_rootElement->getText();
            preg_match("`$date([^\\n]*\\n){1,5}`", $text, $match);
        }
        foreach ($products as $product) {
            if (isset($match[0]) && !strstr($match[0], $product->getName())) {
                continue;
            }
            $productLocator = sprintf($this->product . $this->productView, $product->getName(), $product->getPrice());
            $views[] = $this->_rootElement->find($productLocator, Locator::SELECTOR_XPATH)->getText();
        }
        return $views;
    }
}
