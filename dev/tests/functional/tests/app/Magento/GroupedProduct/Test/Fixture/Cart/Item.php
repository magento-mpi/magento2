<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GroupedProduct\Test\Fixture\Cart;

use Magento\GroupedProduct\Test\Fixture\GroupedProductInjectable;
use Mtf\Fixture\FixtureInterface;
use Magento\Bundle\Test\Fixture\BundleProduct;

/**
 * Class Item
 * Data for verify cart item block on checkout page
 *
 * Data keys:
 *  - product (fixture data for verify)
 */
class Item extends \Magento\Catalog\Test\Fixture\Cart\Item
{
    /**
     * @constructor
     * @param FixtureInterface $product
     */
    public function __construct(FixtureInterface $product)
    {
        parent::__construct($product);

        /** @var GroupedProductInjectable $product */
        $associatedProducts = [];
        $cartItem = [];

        foreach ($product->getAssociated()['products'] as $key => $product) {
            $key = 'product_key_' . $key;
            $associatedProducts[$key] = $product;
        }

        // Replace key in checkout data
        foreach ($this->data as $fieldName => $fieldValues) {
            foreach ($fieldValues as $key => $value) {
                $product = $associatedProducts[$key];
                $cartItem[$fieldName][$product->getSku()] = $value;
            }
        }

        // Add empty "options" field
        foreach ($associatedProducts as $product) {
            $cartItem['options'][$product->getSku()] = [];
        }

        $this->data = array_replace($this->data, $cartItem);
    }

    /**
     * Persist fixture
     *
     * @return void
     */
    public function persist()
    {
        //
    }

    /**
     * Return prepared data set
     *
     * @param string $key [optional]
     * @return mixed
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getData($key = null)
    {
        return $this->data;
    }

    /**
     * Return data set configuration settings
     *
     * @return string
     */
    public function getDataConfig()
    {
        //
    }
}
