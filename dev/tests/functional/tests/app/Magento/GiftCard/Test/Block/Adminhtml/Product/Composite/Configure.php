<?php
/**
 * {license_notice}
 *
 * @spi
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCard\Test\Block\Adminhtml\Product\Composite;

use Mtf\Fixture\FixtureInterface;

/**
 * Class Configure
 * Adminhtml gift card product composite configure block
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
        $giftCardAmounts = $fields['giftcard_amounts'];
        if (isset($checkoutData['giftcard_options'])) {
            $productOptions = array_merge($productOptions, $checkoutData['giftcard_options']);
            $keyAmount = str_replace('option_key_', '', $productOptions['giftcard_amount']);
            $productOptions['giftcard_amount'] = $giftCardAmounts[$keyAmount]['price'];
        }

        return parent::dataMapping($productOptions);
    }
}
