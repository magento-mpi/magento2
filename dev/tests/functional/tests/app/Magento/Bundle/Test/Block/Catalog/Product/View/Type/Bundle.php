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

namespace Magento\Bundle\Test\Block\Catalog\Product\View\Type;

use Mtf\Factory\Factory;
use Mtf\Client\Element;
use Mtf\Client\Element\Locator;
use Magento\Catalog\Test\Block\Product\View\Options;

/**
 * Class Bundle
 * Catalog bundle product info block
 *
 * @package Magento\Bundle\Test\Block\Catalog\Product\View\Type
 */
class Bundle extends Options
{
    /**
     * Fill bundle options
     *
     * @param array $bundleOptions
     */
    public function fillBundleOptions($bundleOptions)
    {
        $index = 1;
        foreach ($bundleOptions as $option) {
            /** @var $optionBlock \Magento\Bundle\Test\Block\Catalog\Product\View\Type\Option\Radio|\Magento\Bundle\Test\Block\Catalog\Product\View\Type\Option\Select */
            $getClass = 'getMagentoBundleCatalogProductViewTypeOption' . ucfirst($option['type']);
            $optionBlock = Factory::getBlockFactory()->$getClass(
                $this->_rootElement->find('.field.option.required:nth-of-type(' . $index++ . ')')
            );
            $optionBlock->fillOption($option);
        }
    }
}
