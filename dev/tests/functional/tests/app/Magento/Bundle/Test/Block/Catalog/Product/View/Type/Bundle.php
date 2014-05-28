<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Bundle\Test\Block\Catalog\Product\View\Type;

use Mtf\Client\Element;
use Mtf\Factory\Factory;
use Magento\Catalog\Test\Block\Product\View\CustomOptions;

/**
 * Class Bundle
 * Catalog bundle product info block
 */
class Bundle extends CustomOptions
{
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
            /** @var $optionBlock \Magento\Bundle\Test\Block\Catalog\Product\View\Type\Option\Radio|
             * \Magento\Bundle\Test\Block\Catalog\Product\View\Type\Option\Select */
            $getClass = 'getMagentoBundleCatalogProductViewTypeOption' . ucfirst($option['type']);
            $optionBlock = Factory::getBlockFactory()->$getClass(
                $this->_rootElement->find('.field.option.required:nth-of-type(' . $index++ . ')')
            );
            $optionBlock->fillOption($option);
        }
    }
}
