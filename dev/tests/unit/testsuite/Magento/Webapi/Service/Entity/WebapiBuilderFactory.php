<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Service\Entity;

class WebapiBuilderFactory extends \Magento\Framework\Serialization\DataBuilderFactory
{
    /**
     * @param \Magento\TestFramework\Helper\ObjectManager $objectManager
     */
    public function __construct(\Magento\TestFramework\Helper\ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Returns a builder for a given class name.
     *
     * @param string $className
     * @return \Magento\Framework\Api\ExtensibleDataBuilderInterface Builder Instance
     */
    public function getDataBuilder($className)
    {
        $interfaceSuffix = 'Interface';
        if (substr($className, -strlen($interfaceSuffix)) === $interfaceSuffix) {
            /** If class name ends with Interface, replace it with Data suffix */
            $builderClassName = substr($className, 0, -strlen($interfaceSuffix)) . 'Data';
        } else {
            $builderClassName = $className;
        }
        $builderClassName .= 'Builder';
        return $this->objectManager->getObject($builderClassName);
    }
}
