<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\Service\Data;

/**
 * Interface for entities which can be extended with custom attributes.
 */
class ExtensibleEntityBuilder implements ExtensibleEntityBuilderInterface
{
    /**
     * @var ExtensibleEntityInterface
     */
    protected $dataModel;

    /**
     * @param ExtensibleEntityInterface $dataModel
     */
    public function __construct(ExtensibleEntityInterface $dataModel)
    {
        //Make sure that the ExtensibleEntityInterface instance preference is not shared in di
        $this->dataModel = $dataModel;
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomAttribute(
        $attributeCode,
        \Magento\Framework\Service\Data\AttributeValueInterface $attributeValue
    ) {
        $this->dataModel->setCustomAttribute($attributeCode, $attributeValue);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomAttributes(array $attributes)
    {
        $this->dataModel->setCustomAttributes($attributes);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function create()
    {
        return $this->dataModel;
    }
}
