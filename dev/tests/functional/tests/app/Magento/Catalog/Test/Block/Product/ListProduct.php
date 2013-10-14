<?php
/**
 * {license_notice}
 *
 * @spi
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Block\Product;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;

/**
 * Class SearchResultsList
 * Product list
 *
 * @package Magento\Catalog\Test\Block\Product
 */
class ListProduct extends Block
{
    /**
     * Product name
     *
     * @var string
     */
    private $productTitle;

    /**
     * Initialize block elements
     */
    protected function _init()
    {
        $this->productTitle = '.product-name';
    }

    /**
     * Check if product with specified name is visible
     *
     * @param string $productName
     * @return bool
     */
    public function isProductVisible($productName)
    {
        return $this->_rootElement->find($this->productTitle, Locator::SELECTOR_CSS)
            ->find('//*[@title="' . $productName .'"]', Locator::SELECTOR_XPATH)
            ->isVisible();
    }
}
