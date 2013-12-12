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
 * Class Price
 * Product Price block
 *
 * @package Magento\Catalog\Test\Block\Product\Price
 */
class Price extends Block
{

    /**
     * MAP block
     *
     * @var string
     */
    protected $mapBlock = '#map-popup-content';

    /**
     * Minimum Advertised Price
     *
     * @var string
     */
    protected $priceMap = "[class='old price'] .price";

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
    protected $addToCart = "[class='action tocart']";

    /**
     * Get Minimum Advertised Price value
     *
     * @return array|string
     */
    public function getOldPrice()
    {
        return $this->_rootElement->find($this->priceMap, Locator::SELECTOR_CSS)->
            getText();
    }

    /**
     * Get actual Price value on frontend
     *
     * @return array|string
     */
    public function getActualPrice()
    {
        return $this->_rootElement->find($this->actualPrice, Locator::SELECTOR_CSS)->
            getText();
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
