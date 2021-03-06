<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Rma\Test\Constraint;

use Magento\Rma\Test\Fixture\Rma;
use Magento\Sales\Test\Fixture\OrderInjectable;
use Mtf\Constraint\AbstractAssertForm;
use Mtf\Fixture\FixtureInterface;

/**
 * Assert that rma is correct display on frontend.
 */
abstract class AbstractAssertRmaOnFrontend extends AbstractAssertForm
{
    /**
     * Default status of rma item.
     */
    const ITEM_DEFAULT_STATUS = 'Pending';

    /**
     * Get rma items.
     *
     * @param Rma $rma
     * @return array
     */
    protected function getRmaItems(Rma $rma)
    {
        $rmaItems = $rma->getItems();
        /** @var OrderInjectable $order */
        $order = $rma->getDataFieldConfig('order_id')['source']->getOrder();
        $orderItems = $order->getEntityId();

        foreach ($rmaItems as $productKey => $productData) {
            $key = str_replace('product_key_', '', $productKey);
            $product = $orderItems[$key];

            $productData['sku'] = $this->prepareProductSku($product);
            $productData['qty'] = $productData['qty_requested'];
            if (!isset($productData['status'])) {
                $productData['status'] = self::ITEM_DEFAULT_STATUS;
            }
            unset($productData['reason']);
            unset($productData['reason_other']);

            $rmaItems[$productKey] = $productData;
        }

        return $rmaItems;
    }

    /**
     * Return product sku.
     *
     * @param FixtureInterface $product
     * @return string
     */
    protected function prepareProductSku(FixtureInterface $product)
    {
        return $product->getSku();
    }
}
