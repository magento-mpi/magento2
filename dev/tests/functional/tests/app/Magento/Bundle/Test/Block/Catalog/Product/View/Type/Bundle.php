<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Bundle\Test\Block\Catalog\Product\View\Type;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;
use Magento\Bundle\Test\Block\Catalog\Product\View\Type\Option;

/**
 * Class Bundle
 * Catalog bundle product info block
 */
class Bundle extends Block
{
    /**
     * Bundle options block
     *
     * @var string
     */
    protected $bundleBlock = '//*[@id="product-options-wrapper"]//fieldset[contains(@class,"bundle")]/div[%d]';

    /**
     * Label item option
     *
     * @var string
     */
    protected $labelOptions = '/label/span[text() = "';

    /**
     * Label item option
     *
     * @var string
     */
    protected $requiredOptions = '[%s(contains(@class,"required"))]';

    /**
     * Label item option
     *
     * @var string
     */
    protected $optionSelect = '//select/option[@value != ""][%d][contains(text(), "%s")]';

    /**
     * Label item option
     *
     * @var string
     */
    protected $optionLabel = '//div[%d][contains(@class, "field")]//*[contains(text(), "%s")]';

    /**
     * Selector DropDown type
     *
     * @var string
     */
    protected $typeDropDown = '//select[contains(@class,"bundle-option-select")]';

    /**
     * Selector Multiselect type
     *
     * @var string
     */
    protected $typeMultiple = '//select[contains(@class,"multiselect")]';

    /**
     * Selector RadioButton type
     *
     * @var string
     */
    protected $typeRadio = '//input[contains(@class,"radio")]';

    /**
     * Selector Checkbox type
     *
     * @var string
     */
    protected $typeCheckbox = '//input[contains(@class,"checkbox")]';

    /**
     * Fill bundle options
     *
     * @param array $bundleOptions
     * @return void
     */
    public function fillBundleOptions($bundleOptions)
    {
        $index = 1;
        foreach ($bundleOptions as $option) {
            /** @var Option $optionBlock */
            $optionBlock = $this->blockFactory->create(
                'Magento\Bundle\Test\Block\Catalog\Product\View\Type\Option\\' . $this->optionConvert($option['type']),
                ['element' => $this->_rootElement->find('.field.option.required:nth-of-type(' . $index++ . ')')]
            );
            $optionBlock->fillOption($option['value']);
        }
    }

    /**
     * Get bundle item option
     *
     * @param Array $fields
     * @param index
     * @return bool|string
     */
    public function displayedBundleItemOption(array $fields, $index)
    {
        $bundleOptionBlock = $this->_rootElement->find(sprintf($this->bundleBlock, $index), Locator::SELECTOR_XPATH);
        $option = $bundleOptionBlock->find(
            $this->{'type' . $this->optionConvert($fields['type'])},
            Locator::SELECTOR_XPATH
        );
        if (!$option->isVisible()) {
            return "This Option type does not equal to fixture option type.";
        }

        $formatRequired = sprintf(
            $this->bundleBlock . $this->requiredOptions,
            $index,
            (($fields['required'] == 'Yes') ? '' : 'not')
        );
        if (!$this->_rootElement->find($formatRequired, Locator::SELECTOR_XPATH)->isVisible()) {
            return "This Option must be " . ($fields['required'] == 'Yes') ? '' : 'not' . " required.";
        }

        $Increment = 1;
        foreach ($fields['items'] as $item) {
            $selectOptions = (($fields['type'] == 'Drop-down' || $fields['type'] == 'Multiple Select')
                && count($fields['items']) > 1) ? $this->optionSelect : $this->optionLabel;
            $formatOption = sprintf($selectOptions, $Increment, $item['name']);
            if (!$bundleOptionBlock->find($formatOption, Locator::SELECTOR_XPATH)->isVisible()) {
                return 'SelectOption ' . $item['name'] .
                ' with index ' . $Increment . ' data is not equals with fixture SelectOption data.';
            }
            $Increment++;
        }
        return true;
    }

    /**
     * Convert string
     *
     * @param string $str
     * @return string
     */
    protected function optionConvert($str)
    {
        if ($end = strpos($str, ' ')) {
            $str = substr($str, 0, $end);
        } elseif ($end = strpos($str, '-')) {
            $str = substr($str, 0, $end) . ucfirst(substr($str, ($end + 1)));
        }
        return $str;
    }
}
