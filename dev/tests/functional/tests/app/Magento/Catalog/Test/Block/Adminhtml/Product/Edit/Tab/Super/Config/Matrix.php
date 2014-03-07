<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Block\Adminhtml\Product\Edit\Tab\Super\Config;

use Mtf\Block\Form;
use Mtf\Client\Element;
use Mtf\Client\Element\Locator;

/**
 * Class Matrix
 * Product variations matrix block
 *
 * @package Magento\Catalog\Test\Block\Adminhtml\Product\Edit\Tab\Super\Config
 */
class Matrix extends Form
{
    /**
     * Fill qty to current variations
     *
     * @param array $variations
     */
    public function fillVariation(array $variations)
    {
        foreach ($variations as $variation) {
            $variationRow = $this->getVariationRow($variation['configurable_attribute']);
            foreach ($variation['value'] as $key => $field) {
                if (!empty($this->mapping[$key])) {
                    $this->_rootElement->find(
                        $variationRow . $this->mapping[$key]['selector'], Locator::SELECTOR_XPATH
                    )->setValue($field['value']);
                }
            }
        }
    }

    /**
     * Define row that clarifies which line in Current Variations grid will be used
     *
     * @param array $variationData
     * @return Element
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
