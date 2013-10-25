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

namespace Magento\Backend\Test\Block\Catalog\Product\Edit\Tab\Super;

use Mtf\Block\Block;
use Mtf\Client\Element;
use Mtf\Client\Element\Locator;

/**
 * Class Attribute
 * Attribute block in Variation section
 *
 * @package Magento\Backend\Test\Block\Catalog\Product\Edit\Tab\Super
 */
class Attribute extends Block
{
    /**
     * Attribute option pricing value
     *
     * @var string
     */
    private $pricingValue;

    /**
     * Attribute option price type button
     *
     * @var string
     */
    private $priceTypeButton;

    /**
     * Attribute option price type value
     *
     * @var string
     */
    private $priceTypeValue;

    /**
     * Attribute option Include checkbox
     *
     * @var string
     */
    private $include;

    /**
     * Initialize block elements
     */
    protected function _init()
    {
        parent::_init();
        //Elements
        $this->pricingValue = '[name*=pricing_value]';
        $this->priceTypeButton = '[data-toggle=dropdown]';
        $this->priceTypeValue = '//*[@data-role="dropdown-menu"]';
        $this->include = '[data-column=include ] [type=checkbox]';
    }

    /**
     * Fill in data to attribute options
     *
     * @param array $fields
     */
    public function fillAttributeOptions(array $fields)
    {
        foreach ($fields as $field) {
            if (isset($field['option_label']['value'])) {
                $optionRow = $this->_rootElement->find('//tr[td="' . $field['option_label']['value'] . '"]',
                    Locator::SELECTOR_XPATH);
                if (isset($field['pricing_value'])) {
                    $optionRow->find($this->pricingValue, Locator::SELECTOR_CSS)
                        ->setValue($field['pricing_value']['value']);
                }
                if ($field['is_percent']['value'] == 'Yes') {
                    $optionRow->find($this->priceTypeButton, Locator::SELECTOR_CSS)->click();
                    $optionRow->find($this->priceTypeValue . '//a[text()="%"]', Locator::SELECTOR_XPATH)->click();
                }
                $optionRow->find($this->include, Locator::SELECTOR_CSS, 'checkbox')
                    ->setValue($field['include']['value']);
            }
        }
    }
}
