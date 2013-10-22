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
use Mtf\Fixture\DataFixture;

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
        return $this->getOptions('#product-options-wrapper', '.field');
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
            $field = $bundleOptionsFieldset->find($fieldDivSelector . ':nth-of-type(' . $index++ . ')');
            $productIndex = 1;
            $productOption = $field->find('select > option:nth-of-type(' . $productIndex . ')');
            while ($productOption->isVisible()){
                $options[$optionName][] = $productOption->getText();
                $productOption = $field->find('select > option:nth-of-type(' . $productIndex++ . ')');
            }
        }
        return $options;
    }
}
