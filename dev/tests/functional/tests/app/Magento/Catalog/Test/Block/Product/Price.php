<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Test\Block\Product;

use Mtf\Block\Block;
use Mtf\Factory\Factory;
use Mtf\Client\Element\Locator;

/**
 * Class Price
 * This class is used to access the price related information from the storefront
 */
class Price extends Block
{
    /**
     * This member holds the class name of the old price block.
     *
     * @var string
     */
    protected $oldPriceClass = '.old-price';

    /**
     * This member holds the class name of the price block that contains the actual price value.
     *
     * @var string
     */
    protected $priceClass = '.price';

    /**
     * This member holds the class name of the regular price block.
     *
     * @var string
     */
    protected $regularPriceClass = '.price-final_price';

    /**
     * This member holds the class name of the special price block.
     *
     * @var string
     */
    protected $specialPriceClass = '.special-price';

    /**
     * Minimum Advertised Price
     *
     * @var string
     */
    protected $priceMap = '.old.price .price';

    /**
     * Actual Price
     *
     * @var string
     */
    protected $actualPrice = '.actual.price .price';

    /**
     * 'Add to Cart' button
     *
     * @var string
     */
    protected $addToCart = '.action.tocart';

    /**
     * 'Close' button
     *
     * @var string
     */
    protected $closeMap = '#map-popup-close';

    /**
     * Price from selector
     *
     * @var string
     */
    protected $priceFromSelector = 'p.price-from span.price';

    /**
     * Price to selector
     *
     * @var string
     */
    protected $priceToSelector = 'p.price-to span.price';

    /**
     * Getting prices
     *
     * @param string $currency
     * @return array
     */
    public function getPrice($currency = '$')
    {
        //@TODO it have to rewrite when will be possibility to divide it to different blocks(by product type)
        $prices = explode("\n", trim($this->_rootElement->getText()));
        if (count($prices) === 1) {
            return ['price_regular_price' => trim($prices[0], $currency)];
        }
        return $this->formatPricesData($prices, $currency);
    }

    /**
     * Formatting data prices
     *
     * @param array $prices
     * @param string $currency
     * @return array
     */
    private function formatPricesData(array $prices, $currency = '$')
    {
        $formatted = [];
        foreach ($prices as $price) {
            list($name, $price) = explode($currency, $price);
            $name = str_replace(' ', '_', trim(preg_replace('#[^0-9a-z]+#i', ' ', strtolower($name)), ' '));
            $formatted['price_' . $name] = $price;
        }
        return $formatted;
    }

    /**
     * This method returns the effective price represented by the block.
     * If a special price is presented, it uses that.
     * Otherwise, the regular price is used.
     *
     * @return string
     */
    public function getEffectivePrice()
    {
        // if a special price is available, then return that
        $priceElement = $this->_rootElement->find($this->specialPriceClass, Locator::SELECTOR_CSS);
        if (!$priceElement->isVisible()) {
            $priceElement = $this->_rootElement->find($this->regularPriceClass, Locator::SELECTOR_CSS);
            if (!$priceElement->isVisible()) {
                $priceElement = $this->_rootElement->find($this->oldPriceClass, Locator::SELECTOR_CSS);
            }
        }
        // return the actual value of the price
        return $priceElement->find($this->priceClass, Locator::SELECTOR_CSS)->getText();
    }

    /**
     * This method returns the regular price represented by the block.
     *
     * @return string
     */
    public function getRegularPrice()
    {
        // either return the old price (implies special price display or a regular price
        $priceElement = $this->_rootElement->find($this->oldPriceClass, Locator::SELECTOR_CSS);
        if (!$priceElement->isVisible()) {
            $priceElement = $this->_rootElement->find($this->regularPriceClass, Locator::SELECTOR_CSS);
        }
        // return the actual value of the price
        return $priceElement->find($this->priceClass, Locator::SELECTOR_CSS)->getText();
    }

    /**
     * This method returns the special price represented by the block.
     *
     * @return string
     */
    public function getSpecialPrice()
    {
        return $this->_rootElement->find(
            $this->specialPriceClass,
            Locator::SELECTOR_CSS
        )->find(
            $this->priceClass,
            Locator::SELECTOR_CSS
        )->getText();
    }

    /**
     * This method returns if the regular price is visible.
     *
     * @return bool
     */
    public function isRegularPriceVisible()
    {
        return $this->_rootElement->find($this->regularPriceClass, Locator::SELECTOR_CSS)->isVisible();
    }

    /**
     * This method returns if the special price is visible.
     *
     * @return bool
     */
    public function isSpecialPriceVisible()
    {
        return $this->_rootElement->find($this->specialPriceClass, Locator::SELECTOR_CSS)->isVisible();
    }

    /**
     * Get Minimum Advertised Price value
     *
     * @return string
     */
    public function getOldPrice()
    {
        return $this->_rootElement->find($this->priceMap, Locator::SELECTOR_CSS)->getText();
    }

    /**
     * Get actual Price value on frontend
     *
     * @param string $currency
     *
     * @return array|float
     */
    public function getActualPrice($currency = '$')
    {
        //@TODO it have to rewrite when will be possibility to divide it to different blocks(by product type)
        $prices = explode("\n", trim($this->_rootElement->find($this->actualPrice, Locator::SELECTOR_CSS)->getText()));
        if (count($prices) === 1) {
            return floatval(trim($prices[0], $currency));
        }
        return $this->formatPricesData($prices, $currency);
    }

    /**
     * Add product to shopping cart from MAP Block
     *
     * @return void
     */
    public function addToCartFromMap()
    {
        $this->_rootElement->find($this->addToCart, Locator::SELECTOR_CSS)->click();
    }

    /**
     * Close MAP Block
     *
     * @return void
     */
    public function closeMapBlock()
    {
        $this->_rootElement->find($this->closeMap, Locator::SELECTOR_CSS)->click();
    }

    /**
     * Get price from
     *
     * @return string
     */
    public function getPriceFrom()
    {
        return $this->_rootElement->find($this->priceFromSelector)->getText();
    }

    /**
     * Get price to
     *
     * @return string
     */
    public function getPriceTo()
    {
        return $this->_rootElement->find($this->priceToSelector)->getText();
    }
}
