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

namespace Magento\Catalog\Test\Block\Adminhtml\Product\Edit\Tab\Super;

use Mtf\Block\Block;
use Mtf\Client\Element;
use Mtf\Client\Element\Locator;

/**
 * Class Attribute
 * Attribute block in Variation section
 *
 * @package Magento\Catalog\Test\Block\Adminhtml\Product\Edit\Tab\Super
 */
class Attribute extends Block
{
    /**
     * Attribute option pricing value
     *
     * @var string
     */
    protected $pricingValue = '[name*=pricing_value]';

    /**
     * Attribute option price type button
     *
     * @var string
     */
    protected $priceTypeButton = '[data-toggle=dropdown]';

    /**
     * Attribute option price type value
     *
     * @var string
     */
    protected $priceTypeValue = '//*[@data-role="dropdown-menu"]';

    /**
     * Attribute option Include checkbox
     *
     * @var string
     */
    protected $include = '[data-column=include ] [type=checkbox]';

    /**
     * Fill in data to attribute options
     *
     * @param array $fields
     */
    public function fillAttributeOptions(array $fields)
    {
        foreach ($fields as $field) {
            if (isset($field['option_label']['value'])) {
                $optionRow = $this->getOptionRow($field['option_label']['value']);

                if (isset($field['pricing_value']['value'])) {
                    $optionRow->find($this->pricingValue, Locator::SELECTOR_CSS)
                        ->setValue($field['pricing_value']['value']);
                }
                if (isset($field['is_percent']['value']) && $field['is_percent']['value'] == 'Yes') {
                    $optionRow->find($this->priceTypeButton, Locator::SELECTOR_CSS)->click();
                    $optionRow->find($this->priceTypeValue . '//a[text()="%"]', Locator::SELECTOR_XPATH)->click();
                }
                $optionRow->find($this->include, Locator::SELECTOR_CSS, 'checkbox')
                    ->setValue($field['include']['value']);
            }
        }
    }

    /**
     * Get option row
     *
     * @param string $optionLabel
     * @return Element
     */
    protected function getOptionRow($optionLabel)
    {
        return $this->_rootElement->find('//tr[td="' . $optionLabel . '"]', Locator::SELECTOR_XPATH);
    }
}
