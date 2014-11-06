<?php
/**
 * Module setup factory. Creates setups used during application install/upgrade.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Module\Updater;

use Magento\Framework\ObjectManager;

class SetupFactory
{
    const INSTANCE_TYPE = 'Magento\Framework\Module\Updater\SetupInterface';

    /**
     * @var ObjectManager
     */
    protected $_objectManager;

    /**
     * @var array
     */
    protected $_resourceTypes;

    /**
     * @param ObjectManager $objectManager
     * @param array $resourceTypes
     */
    public function __construct(ObjectManager $objectManager, array $resourceTypes)
    {
        $this->_objectManager = $objectManager;
        $this->_resourceTypes = $resourceTypes;
    }

    /**
     * @param string $resourceName
     * @param string $moduleName
     * @param \Magento\Framework\Module\ResourceInterface $resource
     * @return SetupInterface
     * @throws \LogicException
     */
    public function create($resourceName, $moduleName, \Magento\Framework\Module\ResourceInterface $resource)
    {
        $className = isset(
            $this->_resourceTypes[$resourceName]
        ) ? $this->_resourceTypes[$resourceName] : 'Magento\Framework\Module\Updater\SetupInterface';

        if (false == is_subclass_of($className, self::INSTANCE_TYPE) && $className !== self::INSTANCE_TYPE) {
            throw new \LogicException($className . ' is not a \Magento\Framework\Module\Updater\SetupInterface');
        }

        return $this->_objectManager
            ->create($className, array('resourceName' => $resourceName, 'moduleName' => $moduleName))
            ->setResource($resource);
    }
}
