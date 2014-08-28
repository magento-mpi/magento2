<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ConfigurableProduct\Test\Block\Product;

use Mtf\Fixture\FixtureInterface;
use Mtf\Fixture\InjectableFixture;
use Magento\ConfigurableProduct\Test\Fixture\ConfigurableProduct;
use Magento\ConfigurableProduct\Test\Fixture\ConfigurableProductInjectable;

/**
 * Class View
 * Product view block on frontend page
 */
class View extends \Magento\Catalog\Test\Block\Product\View
{
    /**
     * Get configurable options block
     *
     * @return \Magento\ConfigurableProduct\Test\Block\Product\View\ConfigurableOptions
     */
    public function getConfigurableOptionsBlock()
    {
        return $this->blockFactory->create(
            'Magento\ConfigurableProduct\Test\Block\Product\View\ConfigurableOptions',
            ['element' => $this->_rootElement]
        );
    }

    /**
     * Fill in the option specified for the product
     *
     * @param FixtureInterface $product
     * @return void
     */
    public function fillOptions(FixtureInterface $product)
    {
        $attributesData = [];
        $checkoutData = [];

        if ($product instanceof InjectableFixture) {
            /** @var ConfigurableProductInjectable $product */
            $attributesData = $product->getConfigurableAttributesData()['attributes_data'];
            $checkoutData = $product->getCheckoutData();

            // Prepare attribute data
            foreach ($attributesData as $attributeKey => $attribute) {
                $attributesData[$attributeKey] = [
                    'type' => $attribute['frontend_input'],
                    'title' => $attribute['label'],
                    'options' => [],
                ];

                foreach ($attribute['options'] as $optionKey => $option) {
                    $attributesData[$attributeKey]['options'][$optionKey] = [
                        'title' => $option['label']
                    ];
                }
            }
        } else {
            /** @var ConfigurableProduct $product */
            $attributesData = $product->getConfigurableAttributes();
            $checkoutData = $product->getCheckoutData();

            // Prepare attributes data
            foreach ($attributesData as $attributeKey => $attribute) {
                $attributesData[$attributeKey] = [
                    'type' => 'dropdown',
                    'title' => $attribute['label']['value']
                ];

                unset($attribute['label']);
                foreach ($attribute as $optionKey => $option) {
                    $attributesData[$attributeKey]['options'][$optionKey] = [
                        'title' => $option['option_label']['value']
                    ];
                }
            }
        }

        $configurableCheckoutData = isset($checkoutData['configurable_options'])
            ? $checkoutData['configurable_options']
            : [];
        $checkoutOptionsData = $this->prepareCheckoutData($attributesData, $configurableCheckoutData);
        $this->getCustomOptionsBlock()->fillCustomOptions($checkoutOptionsData);

        parent::fillOptions($product);
    }

    /**
     * Return product options
     *
     * @param FixtureInterface $product [optional]
     * @return array
     */
    public function getOptions(FixtureInterface $product = null)
    {
        $options = [
            'configurable_options' => $this->getConfigurableOptionsBlock()->getOptions($product)
        ];
        $options += parent::getOptions($product);

        return $options;
    }
}
