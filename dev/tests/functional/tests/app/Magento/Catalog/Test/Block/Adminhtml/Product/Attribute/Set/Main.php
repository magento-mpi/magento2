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

/**
 * Class Main
 * Attribute Set Main block
 */
class Main extends Block
{
    /**
     * Attribute Groups
     *
     * @var string
     */
    protected $groups = './/*[contains(@class,"x-tree-root-node")]//li[@class="x-tree-node"]/div/a/span[text()="%s"]';

    /**
     * Attribute that will be added to the group
     *
     * @var string
     */
    protected $attribute = './/*[contains(@class,"x-tree-root-node")]//div/a/span[text()="%s"]';

    /**
     * Move Attribute to Attribute Group
     *
     * @param array $attributeData
     * @param string $attributeGroup
     * @return void
     */
    public function moveAttribute($attributeData, $attributeGroup)
    {
        if (isset($attributeData['attribute_code'])) {
            $attribute = $attributeData['attribute_code'];
        } else {
            $attribute = strtolower($attributeData['frontend_label']);
        }

        $attributeGroupLocator = sprintf($this->groups, $attributeGroup);
        $target = $this->_rootElement->find($attributeGroupLocator, Locator::SELECTOR_XPATH);

        $attributeLabelLocator = sprintf($this->attribute, $attribute);
        $this->_rootElement->find($attributeLabelLocator, Locator::SELECTOR_XPATH)->dragAndDrop($target);
    }
}
