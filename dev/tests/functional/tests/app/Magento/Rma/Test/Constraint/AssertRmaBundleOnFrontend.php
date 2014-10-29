<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Rma\Test\Constraint;

use Mtf\Fixture\FixtureInterface;
use Magento\Rma\Test\Fixture\Rma;
use Magento\Sales\Test\Fixture\OrderInjectable;
use Magento\Bundle\Test\Fixture\BundleProduct;

/**
 * Assert that rma with item as bundle product is correct display on frontend (MyAccount - My Returns).
 */
class AssertRmaBundleOnFrontend extends AssertRmaOnFrontend
{
    /**
     * Get items of rma.
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
        $result = [];

        foreach ($rmaItems as $productKey => $productData) {
            $key = str_replace('product_key_', '', $productKey);
            $product = $orderItems[$key];
            $skuProductItems = $this->getSkuProductItems($product);

            foreach ($skuProductItems as $sku) {
                $itemData = $productData;
                $itemData['sku'] = $sku;
                $itemData['qty'] = $productData['qty_requested'];
                if (!isset($itemData['status'])) {
                    $itemData['status'] = 'Pending';
                }
                unset($itemData['reason']);

                $result[] = $itemData;
            }
        }

        return $result;
    }

    /**
     * Get items sku of bundle product.
     *
     * @param FixtureInterface $product
     * @return array
     */
    protected function getSkuProductItems(FixtureInterface $product)
    {
        /** @var BundleProduct $product */
        $bundleSelections = $product->getBundleSelections();
        $checkoutData = $product->getCheckoutData();
        $checkoutOptions = isset($checkoutData['options']['bundle_options'])
            ? $checkoutData['options']['bundle_options']
            : [];
        $result = [];

        foreach ($checkoutOptions as $option) {
            foreach ($bundleSelections['products'] as $optionProducts) {
                foreach ($optionProducts as $productItem) {
                    if (false !== strpos($productItem->getName(), $option['value']['name'])) {
                        $result[] = $productItem->getSku();
                    }
                }
            }
        }

        return $result;
    }
}
