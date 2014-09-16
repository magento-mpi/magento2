<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ConfigurableProduct\Test\Block\Product\View;

use Mtf\Client\Element;
use Mtf\Client\Element\Locator;
use Magento\Catalog\Test\Block\Product\View\CustomOptions;
use Mtf\Fixture\FixtureInterface;
use Magento\ConfigurableProduct\Test\Fixture\ConfigurableProductInjectable;

/**
 * Class ConfigurableOptions
 * Form of configurable options product
 */
class ConfigurableOptions extends CustomOptions
{
    /**
     * Get configurable product options
     *
     * @param FixtureInterface|null $product [optional]
     * @return array
     * @throws \Exception
     */
    public function getOptions(FixtureInterface $product)
    {
        /** @var ConfigurableProductInjectable $product */
        $attributesData = $product->hasData('configurable_attributes_data')
            ? $product->getConfigurableAttributesData()['attributes_data']
            : [];
        $listOptions = $this->getListOptions();
        $result = [];

        foreach ($attributesData as $option) {
            $title = $option['label'];
            if (!isset($listOptions[$title])) {
                throw new \Exception("Can't find option: \"{$title}\"");
            }

            /** @var Element $optionElement */
            $optionElement = $listOptions[$title];
            $typeMethod = preg_replace('/[^a-zA-Z]/', '', $option['frontend_input']);
            $getTypeData = 'get' . ucfirst(strtolower($typeMethod)) . 'Data';

            $optionData = $this->$getTypeData($optionElement);
            $optionData['title'] = $title;
            $optionData['type'] = $option['frontend_input'];
            $optionData['is_require'] = $optionElement->find($this->required, Locator::SELECTOR_XPATH)->isVisible()
                ? 'Yes'
                : 'No';

            $result[$title] = $optionData;
        }

        return $result;
    }
}
