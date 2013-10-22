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

namespace Magento\Bundle\Test\Block\Catalog\Product;

use Mtf\Factory\Factory;
use Mtf\Client\Element\Locator;
use \Magento\Catalog\Test\Block\Product\View as ProductView;

/**
 * Class View
 * Bundle View block
 *
 * @package Magento\Bundle\Test\Block\Product
 */
class View extends ProductView
{
    /**
     * Return product price displayed on page
     *
     * @return array Returns arrays with keys corresponding to fixture keys
     */
    public function getProductPrice()
    {
        $priceFromTo = $this->_getPriceFromTo();
        return empty($priceFromTo) ? array('price' => parent::getProductPrice()) : $priceFromTo;
    }

    /**
     * Get bundle product price in form "From: To:"
     *
     * @return array F.e. array('price_from' => '$110', 'price_to' => '$120')
     */
    protected function _getPriceFromTo() {
        $priceFrom = $this->_rootElement->find('.price-from');
        $priceTo = $this->_rootElement->find('.price-to');
        $price = array();
        if ($priceFrom->isVisible()) {
            $price['price_from'] = $priceFrom->find('.price')->getText();
        }
        if ($priceTo->isVisible()) {
            $price['price_to'] = $priceTo->find('.price')->getText();
        }
        return $price;
    }
}
