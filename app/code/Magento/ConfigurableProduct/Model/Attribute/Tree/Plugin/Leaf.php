<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ConfigurableProduct\Model\Attribute\Tree\Plugin;

class Leaf
{
    /**
     * @var \Magento\ConfigurableProduct\Model\Resource\Product\Type\Configurable\AttributeFactory
     */
    protected $_attributeFactory;

    /**
     * @param \Magento\ConfigurableProduct\Model\Resource\Product\Type\Configurable\AttributeFactory $attributeFactory
     */
    public function __construct(
        \Magento\ConfigurableProduct\Model\Resource\Product\Type\Configurable\AttributeFactory $attributeFactory
    ) {
        $this->_attributeFactory = $attributeFactory;
    }

    /**
     * Mark configurable attributes properly for attribute tree
     *
     * @param array $arguments
     * @param \Magento\Code\Plugin\InvocationChain $invocationChain
     * @return mixed
     */
    public function aroundGetData(array $arguments, \Magento\Code\Plugin\InvocationChain $invocationChain)
    {
        $child = $arguments[0];
        $attributeSetId = $arguments[1];
        $result = $invocationChain->proceed($arguments);

        $configurable = $this->_attributeFactory->create()->getUsedAttributes($attributeSetId);
        $result['is_configurable'] = (int)in_array($child->getAttributeId(), $configurable);

        return $result;
    }
}
