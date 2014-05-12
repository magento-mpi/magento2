<?php
/**
 * {license_notice}
 *
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
        return $this->getOptions('.fieldset > .field');
    }

    /**
     * Get bundle custom options
     *
     * @return array
     */
    public function getBundleCustomOptions()
    {
        return $this->getOptions('#product-options-wrapper > .fieldset > .field');
    }

    /**
     * Get bundle options
     *
     * @return array
     */
    public function getBundleOptions()
    {
        return $this->getOptions('.fieldset.bundle.options > .field');
    }

    /**
     * Get options from specified fieldset using specified fieldDiv selector
     *
     * @param string $fieldSelector
     * @return array
     */
    protected function getOptions($fieldSelector = '.fieldset.bundle.options')
    {
        $index = 1;
        $options = [];
        $field = $this->_rootElement->find($fieldSelector . ':nth-of-type(' . $index . ')');
        while ($field->isVisible()) {
            $optionName = $field->find('label > span')->getText();
            $options[$optionName] = [];
            $productIndex = 1;
            $productOption = $field->find('select > option:nth-of-type(' . $productIndex . ')');
            while ($productOption->isVisible()) {
                $options[$optionName][] = trim($productOption->getText());
                $productIndex++;
                $productOption = $field->find('select > option:nth-of-type(' . $productIndex . ')');
            }
            $index++;
            $field = $this->_rootElement->find($fieldSelector . ':nth-of-type(' . $index . ')');
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
                '//*[*[@class="product options wrapper"]//span[text()="' .
                $attributeLabel .
                '"]]//select',
                Locator::SELECTOR_XPATH,
                'select'
            );
            $select->setValue($attributeValue);
        }
    }

    /**
     * Choose custom option in a drop down
     *
     * @param string $productOption
     */
    public function selectProductCustomOption($productOption)
    {
        $select = $this->_rootElement->find(
            '//*[@class="product options wrapper"]//option[text()="' . $productOption . '"]/..',
            Locator::SELECTOR_XPATH,
            'select'
        );
        $select->setValue($productOption);
    }
}
