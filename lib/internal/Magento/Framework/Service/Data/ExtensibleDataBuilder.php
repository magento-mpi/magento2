<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\Service\Data;

use Magento\Framework\Api\Data\ExtensibleDataBuilderInterface;
use Magento\Framework\Model\AbstractExtensibleModel;
use Magento\Framework\ObjectManager;

/**
 * Implementation for \Magento\Framework\Api\Data\ExtensibleDataBuilderInterface.
 */
class ExtensibleDataBuilder implements ExtensibleDataBuilderInterface
{
    /**
     * @var string
     */
    protected $modelClassInterface;

    /**
     * @var array
     */
    protected $data;

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * Initialize the builder
     *
     * @param ObjectManager $objectManager
     * @param string $modelClassInterface
     */
    public function __construct(ObjectManager $objectManager, $modelClassInterface)
    {
        $this->objectManager = $objectManager;
        $this->modelClassInterface = $modelClassInterface;
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomAttribute(\Magento\Framework\Api\Data\AttributeInterface $attribute)
    {
        // Store as an associative array for easier lookup and processing
        $this->data[AbstractExtensibleModel::CUSTOM_ATTRIBUTES_KEY][$attribute->getAttributeCode()]
            = $attribute;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomAttributes(array $attributes)
    {
        foreach ($attributes as $attribute) {
            $this->setCustomAttribute($attribute);
        }
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function create()
    {
        return $this->objectManager->create(
            $this->modelClassInterface,
            ['data' => $this->data]
        );
    }
}
