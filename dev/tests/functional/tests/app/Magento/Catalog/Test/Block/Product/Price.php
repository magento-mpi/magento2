<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Catalog\Test\Block\Product;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;

/**
 * Class Price
 *
 * This class is used to access the price related information from the storefront.
 *
 * @package Magento\Catalog\Test\Block\Product
 */
class Price extends Block
{
    /**
     * This member holds the class name of the old price block.
     *
     * @var string
     */
    protected $oldPriceClass = 'old-price';

    /**
     * This member holds the class name of the price block that contains the actual price value.
     *
     * @var string
     */
    protected $priceClass = 'price';

    /**
     * This member holds the class name of the regular price block.
     *
     * @var string
     */
    protected $regularPriceClass = "regular-price";

    /**
     * This member holds the class name of the special price block.
     *
     * @var string
     */
    protected $specialPriceClass = 'special-price';

    /**
     * This method returns the effective price represented by the block. If a special price is presented, it uses that.
     * Otherwise, the regular price is used.
     */
    public function getEffectivePrice() {
        // if a special price is available, then return that
        $priceElement = $this->_rootElement->find($this->specialPriceClass, Locator::SELECTOR_CLASS_NAME);
        if (!$priceElement->isVisible()) {
            $priceElement = $this->_rootElement->find($this->regularPriceClass, Locator::SELECTOR_CLASS_NAME);
            if (!$priceElement->isVisible()) {
                $priceElement = $this->_rootElement->find($this->oldPriceClass, Locator::SELECTOR_CLASS_NAME);
            }
        }
        // return the actual value of the price
        return $priceElement->find($this->priceClass, Locator::SELECTOR_CLASS_NAME)->getText();
    }

    /**
     * This method returns the regular price represented by the block.
     */
    public function getRegularPrice() {
        // either return the old price (implies special price display or a regular price
        $priceElement = $this->_rootElement->find($this->oldPriceClass, Locator::SELECTOR_CLASS_NAME);
        if (!$priceElement->isVisible()) {
            $priceElement = $this->_rootElement->find($this->regularPriceClass, Locator::SELECTOR_CLASS_NAME);
        }
        // return the actual value of the price
        return $priceElement->find($this->priceClass, Locator::SELECTOR_CLASS_NAME)->getText();
    }

    /**
     * This method returns the special price represented by the block.
     */
    public function getSpecialPrice() {
        return $this->_rootElement->find($this->specialPriceClass, Locator::SELECTOR_CLASS_NAME)->find($this->priceClass, Locator::SELECTOR_CLASS_NAME)->getText();
    }
}
