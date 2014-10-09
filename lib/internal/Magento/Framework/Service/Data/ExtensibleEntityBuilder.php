<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\Service\Data;

use Magento\Framework\Api\ExtensibleDataBuilderInterface;
use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface for entities which can be extended with custom attributes.
 */
class ExtensibleDataBuilder implements ExtensibleDataBuilderInterface
{
    /**
     * @var ExtensibleDataInterface
     */
    protected $dataModel;

    /**
     * @param ExtensibleDataInterface $dataModel
     */
    public function __construct(ExtensibleDataInterface $dataModel)
    {
        //Make sure that the ExtensibleDataInterface instance preference is not shared in di
        $this->dataModel = $dataModel;
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomAttribute(
        $attributeCode,
        \Magento\Framework\Api\AttributeInterface $attributeValue
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
