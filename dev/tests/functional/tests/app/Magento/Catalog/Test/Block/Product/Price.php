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
}
