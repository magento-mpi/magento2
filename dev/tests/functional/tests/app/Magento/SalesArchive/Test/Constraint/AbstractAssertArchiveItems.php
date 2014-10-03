<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\SalesArchive\Test\Constraint;

use Mtf\Constraint\AbstractAssertForm;
use Mtf\Page\BackendPage;
use Magento\Sales\Test\Fixture\OrderInjectable;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;

/**
 * Class AbstractAssertArchiveItems
 * Assert items represented in archived entity view page
 */
abstract class AbstractAssertArchiveItems extends AbstractAssertForm
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
     * @return array
     */
    protected function prepareOrderProducts(OrderInjectable $order)
    {
        $products = $order->getEntityId()['products'];
        $productsData = [];

        /** @var CatalogProductSimple $product */
        foreach ($products as $product) {
            $productsData[] = [
                'product' => $product->getName(),
                'sku' => $product->getSku(),
                'qty' => $product->getCheckoutData()['options']['qty']
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
