<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Constraint;

use Mtf\Constraint\AbstractAssertForm;
use Magento\Sales\Test\Fixture\OrderInjectable;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;

/**
 * Class AbstractAssertArchiveItems
 * Assert items represented in order's entity view page
 */
abstract class AbstractAssertItems extends AbstractAssertForm
{
    /**
     * Key for sort data
     *
     * @var string
     */
    protected $sortKey = "::sku";

    /**
     * List compare fields
     *
     * @var array
     */
    protected $compareFields = [
        'product',
        'sku',
        'qty'
    ];

    /**
     * Prepare order products
     *
     * @param OrderInjectable $order
     * @param array|null $data [optional]
     * @return array
     */
    protected function prepareOrderProducts(OrderInjectable $order, array $data = null)
    {
        $products = $order->getEntityId()['products'];
        $productsData = [];

        /** @var CatalogProductSimple $product */
        foreach ($products as $key => $product) {
            $productsData[] = [
                'product' => $product->getName(),
                'sku' => $product->getSku(),
                'qty' => (isset($data[$key]['qty']) && $data[$key]['qty'] != '-')
                    ? $data[$key]['qty']
                    : $product->getCheckoutData()['options']['qty']
            ];
        }

        return $this->sortDataByPath($productsData, $this->sortKey);
    }

    /**
     * Prepare invoice data
     *
     * @param array $itemsData
     * @return array
     */
    protected function preparePageItems(array $itemsData)
    {
        foreach ($itemsData as $key => $itemData) {
            $itemsData[$key] = array_intersect_key($itemData, array_flip($this->compareFields));
        }
        return $this->sortDataByPath($itemsData, $this->sortKey);
    }
}
