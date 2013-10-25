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

namespace Magento\Backend\Test\Block\Catalog\Product\Edit\Tab\Super\Config;

use Mtf\Block\Form;
use Mtf\Client\Element;
use Mtf\Client\Element\Locator;

/**
 * Class Matrix
 * Product variations matrix block
 *
 * @package Magento\Backend\Test\Block\Catalog\Product\Edit\Tab\Super\Config
 */
class Matrix extends Form
{
    /**
     * Variation name
     *
     * @var string
     */
    private $name;

    /**
     * Variation sku
     *
     * @var string
     */
    private $sku;

    /**
     * Variation qty
     *
     * @var string
     */
    private $qty;

    /**
     * Variation weight
     *
     * @var string
     */
    private $weight;

    /**
     * Initialize block elements
     */
    protected function _init()
    {
        parent::_init();
        //Elements
        $this->name = '//*[@data-column="name"]/input';
        $this->sku = '//*[@data-column="sku"]/input';
        $this->qty = '//*[@data-column="qty"]/input';
        $this->weight = '//*[@data-column="weight"]/input';
    }

    /**
     * Fill qty to current variations
     *
     * @param array $variations
     */
    public function fillVariation(array $variations)
    {
        foreach ($variations as $variation) {
            $variationRow = $this->_getVariationRow($variation['configurable_attribute']);
            if (isset($variation['name'])) {
                $this->_rootElement->find($variationRow . $this->name, Locator::SELECTOR_XPATH)
                    ->setValue($variation['name']['value']);
            }
            if (isset($variation['sku'])) {
                $this->_rootElement->find($variationRow . $this->sku, Locator::SELECTOR_XPATH)
                    ->setValue($variation['sku']['value']);
            }
            if (isset($variation['quantity_and_stock_status']['qty'])) {
                $this->_rootElement->find($variationRow . $this->qty, Locator::SELECTOR_XPATH)
                    ->setValue($variation['quantity_and_stock_status']['qty']['value']);
            }
            if (isset($variation['weight'])) {
                $this->_rootElement->find($variationRow . $this->weight, Locator::SELECTOR_XPATH)
                    ->setValue($variation['weight']['value']);
            }
        }
    }

    /**
     * Define row that clarifies which line in Current Variations grid will be used
     *
     * @param array $variationData
     * @return Element
     */
    private function _getVariationRow(array $variationData)
    {
        $options = array();
        foreach ($variationData as $attributeData) {
            $options[] = 'td[text()="' . $attributeData['attribute_option'] . '"]';
        }

        return '//tr[' . implode(' and ', $options) . ']';
    }
}
