<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
namespace Magento\Tools\SampleData;

use Magento\Framework\ObjectManager;

class SetupFactory
{
    const INSTANCE_TYPE = 'Magento\Tools\SampleData\SetupInterface';

    /**
     * @var ObjectManager
     */
    protected $_objectManager;

    /**
     * @param ObjectManager $objectManager
     */
    public function __construct(ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * @param string $resourceType
     * @return SetupInterface
     * @throws \LogicException
     */
    public function create($resourceType)
    {
        if (false == is_subclass_of($resourceType, self::INSTANCE_TYPE) && $resourceType !== self::INSTANCE_TYPE) {
            throw new \LogicException($resourceType . ' is not a ' . self::INSTANCE_TYPE);
        }

        return $this->_objectManager->create($resourceType);
    }
}