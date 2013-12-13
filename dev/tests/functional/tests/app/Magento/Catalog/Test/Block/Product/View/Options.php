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

/**
 * Class Bundle
 * Catalog bundle product info block
 *
 * @package Magento\Catalog\Test\Block\Catalog\Product\View\Options
 */
class Options extends Block
{
    /**
     * Get product custom options
     *
     * @return array
     */
    public function getProductCustomOptions()
    {
        return $this->getOptions('.fieldset', '.field');
    }

    /**
     * Get bundle options
     *
     * @return array
     */
    public function getBundleOptions()
    {
        return $this->getOptions('.fieldset.bundle.options', '.field');
    }

    /**
     * Get options from specified fieldset using specified fieldDiv selector
     *
     * @param string $fieldsetSelector
     * @param string $fieldDivSelector
     * @return array
     */
    protected function getOptions($fieldsetSelector = '.fieldset.bundle.options', $fieldDivSelector = '.field')
    {
        $bundleOptionsFieldset = $this->_rootElement->find($fieldsetSelector);
        $index = 1;
        $options = array();
        $field = $bundleOptionsFieldset->find($fieldDivSelector . ':nth-of-type(' . $index . ')');
        while ($field->isVisible()) {
            $optionName = $field->find('label > span')->getText();
            $options[$optionName] = array();
            $productIndex = 1;
            $productOption = $field->find('select > option:nth-of-type(' . $productIndex . ')');
            while ($productOption->isVisible()) {
                $options[$optionName][] = trim($productOption->getText());
                $productIndex++;
                $productOption = $field->find('select > option:nth-of-type(' . $productIndex . ')');
            }
            $index++;
            $field = $bundleOptionsFieldset->find($fieldDivSelector . ':nth-of-type(' . $index . ')');
        }
        return $options;
    }

    /**
     * Fill configurable product options
     *
     * @param array $productOptions
     */
    public function fillProductOptions($productOptions)
    {
        foreach ($productOptions as $attributeLabel => $attributeValue) {
            $select = $this->_rootElement->find(
                '//*[*[@class="product options configure"]//span[text()="' .
                $attributeLabel .
                '"]]//select',
                Locator::SELECTOR_XPATH,
                'select'
            );
            $select->setValue($attributeValue);
        }
    }
}
