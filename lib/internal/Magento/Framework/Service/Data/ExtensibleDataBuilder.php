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
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Model\AbstractExtensibleModel;

/**
 * Interface for entities which can be extended with custom attributes.
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
    public function setCustomAttribute(\Magento\Framework\Api\AttributeInterface $attribute)
    {
        $this->data[AbstractExtensibleModel::CUSTOM_ATTRIBUTES_KEY][$attribute->getAttributeCode()]
            = $attribute->getValue();
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomAttributes(array $attributes)
    {
        foreach ($attributes as $attribute) {
            $this->setCustomAttributes($attribute);
        }
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function create()
    {
        return $this->objectManager->create($this->modelClassInterface, ['data' => $this->data]);
    }
}
