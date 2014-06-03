<?php
/**
 * Created by PhpStorm.
 * User: oonoshko
 * Date: 02.06.14
 * Time: 12:58
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
