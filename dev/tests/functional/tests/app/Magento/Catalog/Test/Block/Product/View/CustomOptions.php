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

namespace Magento\Catalog\Test\Block\Product\View;

use Mtf\Block\Block;
use Mtf\Client\Element;
use Mtf\Client\Element\Locator;
use Mtf\Factory\Factory;

/**
 * Class Custom Options
 *
 * @package Magento\Catalog\Test\Block\Catalog\Product\View
 */
class CustomOptions extends Block
{
    /**
     * Field set selector
     *
     * @var string
     */
    protected $fieldsetSelector = '.fieldset';

    /**
     * Row selector
     *
     * @var string
     */
    protected $rowSelector = '.field';

    /**
     * Get product options
     *
     * @return array
     */
    public function getOptions()
    {
        $options = [];
        $index = 1;

        while (($fieldElement = $this->_rootElement->find(
                '//*[@id="product-options-wrapper"]//*[@class="fieldset"]'
                . '/div[not(contains(@class,"downloads")) and contains(@class,"field")][' . $index . ']',
                Locator::SELECTOR_XPATH))
            && $fieldElement->isVisible()
        ) {
            $option = ['price' => []];
            $option['is_require'] = $this->_rootElement->find(
                '//*[@id="product-options-wrapper"]//*[@class="fieldset"]'
                . '/div[not(contains(@class,"downloads")) and contains(@class,"field")'
                . ' and contains(@class,"required")][' . $index . ']',
                Locator::SELECTOR_XPATH
            )->isVisible();
            $option['title'] = $fieldElement->find('.label span:not(.price-notice)')->getText();

            if (($price = $fieldElement->find('.label .price-notice'))
                && $price->isVisible()
            ) {
                $matches = [];
                $priceValue = $price->getText();
                if (preg_match('#\$(\d+\.\d+)$#', $priceValue, $matches)) {
                    $option['price'][] = $matches[1];
                }
            } elseif (($prices = $fieldElement->find(
                    './/div[contains(@class,"control")]/select',
                    Locator::SELECTOR_XPATH))
                && $prices->isVisible()
            ) {
                $priceIndex = 0;
                while (($price = $prices->find('.//option[' . ++$priceIndex . ']', Locator::SELECTOR_XPATH))
                    && $price->isVisible()
                ) {
                    $matches = [];
                    $priceValue = $price->getText();
                    if (preg_match('#\$(\d+\.\d+)$#', $priceValue, $matches)) {
                        $option['price'][] = $matches[1];
                    }
                }
            }

            $options[] = $option;
            $index += 1;
        }

        return $options;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function get()
    {
        $optionsFieldset = $this->_rootElement->find($this->fieldsetSelector);
        $fieldsetIndex = 1;
        $options = array();
        //@todo move to separate block
        $field = $optionsFieldset->find($this->rowSelector . ':nth-of-type(' . $fieldsetIndex . ')');
        while ($field->isVisible()) {
            $optionFieldset = [];
            $optionFieldset['title'] = $field->find('.label')->getText();
            $optionFieldset['is_require'] = $field->find('select.required')->isVisible();
            $options[] = & $optionFieldset;
            $optionIndex = 1;
            //@todo move to separate block
            $option = $field->find('select > option:nth-of-type(' . $optionIndex . ')');
            while ($option->isVisible()) {
                if (preg_match('~^(?<title>.+) .?\$(?P<price>\d+\.\d*)$~', $option->getText(), $matches) !== false
                    && !empty($matches['price'])
                ) {
                    $optionFieldset['options'][] = [
                        'title' => $matches['title'],
                        'price' => $matches['price'],
                    ];
                };
                $optionIndex++;
                $option = $field->find('select > option:nth-of-type(' . $optionIndex . ')');
            }
            $fieldsetIndex++;
            $field = $optionsFieldset->find($this->rowSelector . ':nth-of-type(' . $fieldsetIndex . ')');
        }
        return $options;
    }
}
