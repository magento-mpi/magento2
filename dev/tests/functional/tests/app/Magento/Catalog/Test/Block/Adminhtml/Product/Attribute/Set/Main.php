<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Block\Adminhtml\Product\Attribute\Set;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;

class Main extends Block
{
    /**
     * Move Attribute to Attribute Group
     *
     * @param string $attributeLabel
     * @return void
     */
    public function moveAttribute($attributeLabel)
    {
        $attributeLabel = strtolower($attributeLabel);
        $target = $this->_rootElement->find(
            ".//*[contains(@class,'x-tree-root-node')]//li[@class='x-tree-node']/div/a/span[text()='Product Details']",
            Locator::SELECTOR_XPATH
        );
        $attributeLabelLocator = sprintf(
            ".//*[contains(@class,'x-tree-root-node')]//li[@class='x-tree-node']/div/a/span[text()='%s']",
            $attributeLabel
        );

        $this->_rootElement->find($attributeLabelLocator, Locator::SELECTOR_XPATH)->dragAndDrop($target);
    }
}
