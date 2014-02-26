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
     * {@inheritdoc}
     */
    protected $_mapping = array(
        'display' => array(
            'selector' => '//*[@data-column="display"]/input',
            'input' => 'checkbox',
        ),
        'name' => array(
            'selector' => '//*[@data-column="name"]/input'
        ),
        'sku' => array(
            'selector' => '//*[@data-column="sku"]/input'
        ),
        'qty' => array(
            'selector' => '//*[@data-column="qty"]/input'
        ),
        'weight' => array(
            'selector' => '//*[@data-column="weight"]/input'
        )
    );

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
                if (!empty($this->_mapping[$key])) {
                    $this->_rootElement->find(
                        $variationRow . $this->_mapping[$key]['selector'],
                        Locator::SELECTOR_XPATH,
                        isset($this->_mapping[$key]['input']) ? $this->_mapping[$key]['input'] : null
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
