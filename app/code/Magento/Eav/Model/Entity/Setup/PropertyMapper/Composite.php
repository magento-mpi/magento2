<?php
/**
 * Composite attribute property mapper
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Eav\Model\Entity\Setup\PropertyMapper;

use Magento\Eav\Model\Entity\Setup\PropertyMapperInterface;
use Magento\ObjectManager;

class Composite implements PropertyMapperInterface
{
    /**
     * @var \Magento\ObjectManager
     */
    protected $objectManager;

    /**
     * @var array
     */
    protected $propertyMappers;

    /**
     * @param ObjectManager $objectManager
     * @param array $propertyMappers
     */
    public function __construct(ObjectManager $objectManager, array $propertyMappers = array())
    {
        $this->objectManager = $objectManager;
        $this->propertyMappers = $propertyMappers;
    }

    /**
     * Map input attribute properties to storage representation
     *
     * @param array $input
     * @param int $entityTypeId
     * @return array
     * @throws \InvalidArgumentException
     */
    public function map(array $input, $entityTypeId)
    {
        $data = array();
        foreach ($this->propertyMappers as $class) {
            if (!is_subclass_of($class, '\Magento\Eav\Model\Entity\Setup\PropertyMapperInterface')) {
                throw new \InvalidArgumentException(
                    'Property mapper ' . $class . ' must'
                        . ' implement \Magento\Eav\Model\Entity\Setup\PropertyMapperInterface'
                );
            }
            $data = array_replace($data, $this->objectManager->get($class)->map($input, $entityTypeId));
        }
        return $data;
    }
}
