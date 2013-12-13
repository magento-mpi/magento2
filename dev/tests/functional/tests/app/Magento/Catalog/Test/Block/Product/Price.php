<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Block\Product;

use Mtf\Block\Block;
use Mtf\Factory\Factory;
use Mtf\Client\Element\Locator;

/**
 * Product Price block
 *
 * @package Magento\Catalog\Test\Block\Product\Price
 */
class Price extends Block
{
    /**
     * @param string $currency
     * @return string|array
     */
    public function getPrice($currency = '$')
    {
        //@TODO it have to rewrite when will be possibility to divide it to different blocks(by product type)
        $prices = explode("\n", trim($this->_rootElement->getText()));
        if (count($prices) == 1) {
            return floatval(trim($prices[0], $currency));
        }
        return $this->formatPricesData($prices, $currency);
    }

    /**
     * @param array $prices
     * @param string $currency
     * @return array
     */
    private function formatPricesData(array $prices, $currency = '$')
    {
        $formatted = array();
        foreach ($prices as $price) {
            list($name, $price) = explode($currency, $price);
            $name = trim(preg_replace('#[^0-9a-z]+#i', ' ', strtolower($name)), ' ');
            $formatted['price_' . $name] = floatval($price);
        }
        return $formatted;
    }

    /**
     * * Minimum Advertised Price
     *
     * @var string
     */
    protected $priceMap = '.old.price .price';

    /**
     * Actual Price
     *
     * @var string
     */
    protected $actualPrice = "[class='regular-price'] .price";

    /**
     * 'Add to Cart' button
     *
     * @var string
     */
    protected $addToCart = '.action.tocart';

    /**
     * Get Minimum Advertised Price value
     *
     * @return array|string
     */
    public function getOldPrice()
    {
        return $this->_rootElement->find($this->priceMap, Locator::SELECTOR_CSS)->getText();
    }

    /**
     * Get actual Price value on frontend
     *
     * @return array|string
     */
    public function getActualPrice()
    {
        return $this->_rootElement->find($this->actualPrice, Locator::SELECTOR_CSS)->getText();
    }

    /**
     * Add product to shopping cart from MAP Block
     *
     */
    public function addToCartFromMap()
    {
        $this->_rootElement->find($this->addToCart, Locator::SELECTOR_CSS)->click();
    }
}
