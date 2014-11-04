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
        if ($this->getDataType() == self::TYPE_DATA_MODEL) {
            $this->currentBuilder = $objectManager->create(
                'Magento\Framework\Api\ExtensibleDataBuilder',
                [
                    'metadataService' => $metadataService,
                    'modelClassInterface' => $modelClassInterface
                ]
            );
        } else {
            $this->currentBuilder = $objectManager->create(
                'Magento\Framework\Api\ExtensibleObjectBuilder',
                [
                    'metadataService' => $metadataService,
                    'modelClassInterface' => $modelClassInterface
                ]
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomAttribute(\Magento\Framework\Api\AttributeInterface $attribute)
    {
        return $this->currentBuilder->setCustomAttribute($attribute);
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomAttributes(array $attributes)
    {
        return $this->currentBuilder->setCustomAttributes($attributes);
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
     */
    public function __call($name, $arguments)
    {
        call_user_func_array([$this->currentBuilder, $name], $arguments);
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
}
