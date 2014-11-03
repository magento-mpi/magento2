<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\Serialization;

use Magento\Framework\ObjectManager;

/**
 * Factory used to construct Data Builder based on interface name
 */
class DataBuilderFactory
{
    /**
     * @param ObjectManager $objectManager
     */
    public function __construct(ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Returns a builder for a given class name.
     *
     * @param string $className
     * @return \Magento\Framework\Api\ExtensibleDataBuilderInterface Builder Instance
     */
    public function getDataBuilder($className)
    {
        $builderClassName = '';
        $interfaceSuffix = 'Interface';
        if (substr($className, -strlen($interfaceSuffix)) === $interfaceSuffix) {
            /** If class name ends with Interface, replace it with Data suffix */
            $builderClassName = substr($className, 0, -strlen($interfaceSuffix)) . 'Data';
        } else {
            $builderClassName = $className;
        }
        $builderClassName .= 'Builder';
        return $this->_objectManager->create($builderClassName);
    }
}