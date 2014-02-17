<?php
/**
 * Product Attribute Group mapper plugin. Adds Configurable product information
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ConfigurableProduct\Model\Entity\Product\Attribute\Group\AttributeMapper;

use Magento\Code\Plugin\InvocationChain;
use Magento\Core\Model\Registry;
use Magento\ConfigurableProduct\Model\Resource\Product\Type\Configurable\AttributeFactory;

class Plugin
{
    /**
     * @var \Magento\ConfigurableProduct\Model\Resource\Product\Type\Configurable\AttributeFactory
     */
    protected $attributeFactory;

    /**
     * @var \Magento\Core\Model\Registry
     */
    protected $registry;

    /**
     * @var array
     */
    protected $configurableAttributes;

    /**
     * @param AttributeFactory $attributeFactory
     * @param Registry $registry
     */
    public function __construct(
        AttributeFactory $attributeFactory,
        Registry $registry
    ) {
        $this->registry = $registry;
        $this->attributeFactory = $attributeFactory;
    }

    /**
     * Add is_configurable field to attribute presentation
     *
     * @param array $arguments
     * @param InvocationChain $invocationChain
     * @return mixed
     */
    public function aroundMap($arguments, InvocationChain $invocationChain)
    {
        $setId = $this->registry->registry('current_attribute_set')->getId();
        $attribute = $arguments[0];
        $result = $invocationChain->proceed($arguments);
        if (!isset($this->configurableAttributes[$setId])) {
            $this->configurableAttributes[$setId] = $this->attributeFactory->create()->getUsedAttributes($setId);
        }
        $result['is_configurable'] = (int)in_array($attribute->getAttributeId(), $this->configurableAttributes[$setId]);
        return $result;
    }
} 
