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
use Mtf\Client\Element;
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
     * This member contains the class identifiers for the product detail block
     * @var string
     */
    protected $productDetails = '.product.details';

    /**
     * Product name
     *
     * @var string
     */
    protected $productTitle = '.product.name';

    /**
     * This method returns the displayed price for the named product.
     * @param string $productName String containing the name of the product to find.
     * @return array|string
     */
    public function getProductPrice($productName)
    {
        return $this->_rootElement->find(
            "//*[@class=\"product details\" and .//*[@title=\"{$productName}\"]]//*[@class=\"price\"]",
            Locator::SELECTOR_XPATH
        )->getText();
    }

    /**
     * This method returns the displayed special price for the named product.
     * @param string $productName String containing the name of the product to find.
     * @return array|string
     */
    public function getProductSpecialPrice($productName)
    {
        return $this->_rootElement->find(
            "//*[@class=\"product details\" and .//*[@title=\"{$productName}\"]]" .
            "//*[@class=\"special-price\"]//*[@class=\"price\"]",
            Locator::SELECTOR_XPATH
        )->getText();
    }

    /**
     * Check if product with specified name is visible
     *
     * @param string $productName
     * @return bool
     */
    public function isProductVisible($productName)
    {
        return $this->getProductNameElement($productName)->isVisible();
    }

    /**
     * Open product view page by clicking on product name
     *
     * @param string $productName
     */
    public function openProductViewPage($productName)
    {
        $this->getProductNameElement($productName)->click();
    }

    /**
     * This method returns the element on the page associated with the product name.
     * @param string $productName String containing the name of the product
     * @return Element
     */
    protected function getProductNameElement($productName)
    {
        return $this->_rootElement->find(
            $this->productTitle,
            Locator::SELECTOR_CSS
        )->find(
            '//*[@title="' . $productName . '"]',
            Locator::SELECTOR_XPATH
        );
    }
}
