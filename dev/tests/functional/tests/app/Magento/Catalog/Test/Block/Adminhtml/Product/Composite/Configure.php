<?php
/**
 * {license_notice}
 *
 * @spi
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Block\Adminhtml\Product\Composite;

use Mtf\Block\Form;
use Mtf\Client\Element\Locator;
use Mtf\Fixture\FixtureInterface;

/**
 * Class Configure
 * Adminhtml catalog product composite configure block
 *
 */
class Configure extends Form
{
    /**
     * Fill options for the product
     *
     * @param FixtureInterface $product
     * @return void
     */
    public function fillOptions(FixtureInterface $product)
    {
        $productOptions = $product->getCheckoutData();
        if (!empty($productOptions['configurable_options'])) {
            $configurableAttributesData = $product->getData('fields/configurable_attributes_data/value');
            $checkoutData = [];

            foreach ($productOptions['configurable_options'] as $optionData) {
                $titleKey = $optionData['title'];
                $valueKey = $optionData['value'];

                $checkoutData[] = [
                    'title' => $configurableAttributesData[$titleKey]['label']['value'],
                    'value' => $configurableAttributesData[$titleKey][$valueKey]['option_label']['value']
                ];
            }

            foreach ($checkoutData as $option) {
                $select = $this->_rootElement->find(
                    '//div[@class="product-options"]//label[text()="' .
                    $option['title'] .
                    '"]//following-sibling::*//select',
                    Locator::SELECTOR_XPATH,
                    'select'
                );
                $select->setValue($option['value']);
            }
            $this->_rootElement->find('.ui-dialog-buttonset button:nth-of-type(2)')->click();
        }
    }
}
