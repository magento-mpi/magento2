<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\Api;

use Magento\Framework\Model\AbstractExtensibleModel;
use Magento\Framework\ObjectManager;
use Magento\Framework\ObjectManager\Config as ObjectManagerConfig;

/**
 * Composite extensible data builder.
 */
class CompositeExtensibleDataBuilder implements ExtensibleDataBuilderInterface
{
    /**#@+
     * Constant which defines if builder is created for building data objects or data models.
     */
    const TYPE_DATA_OBJECT = 'data_object';
    const TYPE_DATA_MODEL = 'data_model';
    /**#@-*/

    /** @var string */
    protected $modelClassInterface;

    /** @var ExtensibleDataBuilderInterface */
    protected $currentBuilder;

    /** @var ObjectManagerConfig */
    protected $objectManagerConfig;

    /**
     * Initialize dependencies.
     *
     * @param ObjectManager $objectManager
     * @param MetadataServiceInterface $metadataService
     * @param ObjectManagerConfig $objectManagerConfig
     * @param string $modelClassInterface
     */
    public function __construct(
        ObjectManager $objectManager,
        MetadataServiceInterface $metadataService,
        ObjectManagerConfig $objectManagerConfig,
        $modelClassInterface
    ) {
        $this->modelClassInterface = $modelClassInterface;
        $this->objectManagerConfig = $objectManagerConfig;
        $arguments = [
            'metadataService' => $metadataService,
            'modelClassInterface' => $modelClassInterface
        ];
        $builderClass = ($this->getDataType() == self::TYPE_DATA_MODEL)
            ? 'Magento\Framework\Api\ExtensibleDataBuilder'
            : 'Magento\Framework\Api\ExtensibleObjectBuilder';
        $this->currentBuilder = $objectManager->create($builderClass, $arguments);
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomAttribute($attributeCode, $attributeValue)
    {
        $this->currentBuilder->setCustomAttribute($attributeCode, $attributeValue);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomAttributes(array $attributes)
    {
        $this->currentBuilder->setCustomAttributes($attributes);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function create()
    {
        return $this->currentBuilder->create();
    }

    /**
     * Proxy all calls to current builder.
     *
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        call_user_func_array([$this->currentBuilder, $name], $arguments);
        return $this;
    }

    /**
     * Identify type of objects which should be built with generated builder. Value can be one of self::TYPE_DATA_*.
     *
     * @return string
     * @throws \LogicException
     */
    protected function getDataType()
    {
        $sourceClassPreference = $this->objectManagerConfig->getPreference($this->modelClassInterface);
        if (empty($sourceClassPreference)) {
            throw new \LogicException(
                "Preference for {$this->modelClassInterface} is not defined."
            );
        }
        if (is_subclass_of($sourceClassPreference, '\Magento\Framework\Api\AbstractSimpleObject')) {
            return self::TYPE_DATA_OBJECT;
        } else if (is_subclass_of($sourceClassPreference, '\Magento\Framework\Model\AbstractExtensibleModel')) {
            return self::TYPE_DATA_MODEL;
        } else {
            throw new \LogicException(
                'Preference of ' . $this->modelClassInterface
                . ' must extend from AbstractSimpleObject or AbstractExtensibleModel'
            );
        }
    }

    /**
     * Populates the fields with data from the array.
     *
     * Keys for the map are snake_case attribute/field names.
     *
     * @param array $data
     * @return $this
     */
    public function populateWithArray(array $data)
    {
        $this->currentBuilder->populateWithArray($data);
        return $this;
    }

    /**
     * Populates the fields with data from the prototype.
     *
     * @param AbstractSimpleObject $prototype
     * @return $this
     */
    public function populate(AbstractSimpleObject $prototype)
    {
        $this->currentBuilder->populate($prototype);
        return $this;
    }
}
