<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ConfigurableProduct\Test\Block\Adminhtml\Product\Edit\Tab\Super\Config;

use Mtf\Block\Form;
use Mtf\Client\Element;
use Mtf\Client\Element\Locator;

/**
 * Class Matrix
 * Product variations matrix block
 */
class Matrix extends Form
{
    /**
     * Fill qty to current variations
     *
     * @param array $variations
     * @return void
     */
    public function fillVariation(array $variations)
    {
        foreach ($variations as $variation) {
            $variationRow = $this->getVariationRow($variation['configurable_attribute']);
            foreach ($variation['value'] as $key => $field) {
                if (!empty($this->mapping[$key])) {
                    $this->_rootElement->find(
                        $variationRow . $this->mapping[$key]['selector'],
                        Locator::SELECTOR_XPATH,
                        isset($this->mapping[$key]['input']) ? $this->mapping[$key]['input'] : null
                    )->setValue($field['value']);
                }
            }
        }
    }

    /**
     * Define row that clarifies which line in Current Variations grid will be used
     *
     * @param array $variationData
     * @return string
     */
    private function getVariationRow(array $variationData)
    {
        $options = array();
        foreach ($variationData as $attributeData) {
            $options[] = 'td[text()="' . $attributeData['attribute_option'] . '"]';
        }

        return '//tr[' . implode(' and ', $options) . ']';
    }
}
