<?php
/**
 * {license_notice}
 *
 * @spi
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GroupedProduct\Test\Block\Adminhtml\Product\Composite;

use Mtf\Fixture\FixtureInterface;

/**
 * Class Configure
 * Adminhtml grouped product composite configure block
 */
class Configure extends \Magento\Catalog\Test\Block\Adminhtml\Product\Composite\Configure
{
    /**
     * Fill options for the product
     *
     * @param FixtureInterface $product
     * @return void
     */
    public function fillOptions(FixtureInterface $product)
    {
        $data = $this->dataMapping($product->getData());
        $this->_fill($data);
    }

    /**
     * Fixture mapping
     *
     * @param array|null $fields
     * @param string|null $parent
     * @return array
     */
    protected function dataMapping(array $fields = null, $parent = null)
    {
        $productOptions = [];
        $checkoutData = $fields['checkout_data']['options'];
        if (count($checkoutData)) {
            $qtyMapping = parent::dataMapping(['qty' => '']);
            $selector = $qtyMapping['qty']['selector'];
            $assignedProducts = $fields['associated']['assigned_products'];
            foreach ($checkoutData as $key => $item) {
                $productName = $assignedProducts[str_replace('product_key_', '', $item['name'])]['name'];
                $qtyMapping['qty']['selector'] = str_replace('%product_name%', $productName, $selector);
                $qtyMapping['qty']['value'] = $item['qty'];
                $productOptions['product_' . $key] = $qtyMapping['qty'];
            }
        }

        return $productOptions;
    }
}
