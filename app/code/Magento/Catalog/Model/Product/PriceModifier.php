<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model\Product;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;

class PriceModifier
{
    /**
     * @param \Magento\Catalog\Model\Product $product
     * @param int $customerGroupId
     * @param int $websiteId
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function removeGroupPrice(\Magento\Catalog\Model\Product $product, $customerGroupId, $websiteId)
    {
        $prices = $product->getData('group_price');
        if (is_null($prices)) {
            throw new NoSuchEntityException("This product doesn't have group price");
        }
        $groupPriceQty = count($prices);

        foreach ($prices as $key => $groupPrice) {
            if (intval($groupPrice['cust_group']) == $customerGroupId
                && intval($groupPrice['website_id']) === $websiteId) {
                unset ($prices[$key]);
            }
        }
        if ($groupPriceQty == count($prices)) {
            throw new NoSuchEntityException(
                "Product hasn't group price with such data: customerGroupId = '$customerGroupId',
                 website = $websiteId."
            );
        }
        $product->setData('group_price', $prices);
        try {
            $product->save();
        } catch (\Exception $exception) {
            throw new CouldNotSaveException("Invalid data provided for group price");
        }
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @param int|string $customerGroupId
     * @param int $qty
     * @param int $websiteId
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function removeTierPrice(\Magento\Catalog\Model\Product $product, $customerGroupId, $qty, $websiteId)
    {
        $prices = $product->getData('tier_price');
        // verify if price exist
        if (is_null($prices)) {
            throw new NoSuchEntityException("This product doesn't have tier price");
        }
        $tierPricesQty = count($prices);

        foreach ($prices as $key => $tierPrice) {
            if ($customerGroupId == 'all' && $tierPrice['price_qty'] == $qty
                && $tierPrice['all_groups'] == 1 && intval($tierPrice['website_id']) === $websiteId) {
                unset ($prices[$key]);
            } elseif ($tierPrice['price_qty'] == $qty && intval($tierPrice['cust_group']) == intval($customerGroupId)
                && intval($tierPrice['website_id']) === $websiteId) {
                unset ($prices[$key]);
            }
        }

        if ($tierPricesQty == count($prices)) {
            throw new NoSuchEntityException(
                "Product hasn't group price with such data: customerGroupId = '$customerGroupId',
                 website = $websiteId, qty = $qty"
            );
        }
        $product->setData('tier_price', $prices);
        try {
            $product->save();
        } catch (\Exception $exception) {
            throw new CouldNotSaveException("Invalid data provided for tier_price");
        }
    }
}
