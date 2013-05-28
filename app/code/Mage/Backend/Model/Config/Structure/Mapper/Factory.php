<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * System Configuration Mapper Factory
 */
class Mage_Backend_Model_Config_Structure_Mapper_Factory
{
    const MAPPER_SORTING                = 'sorting';
    const MAPPER_PATH                   = 'path';
    const MAPPER_IGNORE                 = 'ignore';
    const MAPPER_DEPENDENCIES           = 'dependencies';
    const MAPPER_ATTRIBUTE_INHERITANCE  = 'attribute_inheritance';
    const MAPPER_EXTENDS                = 'extends';

    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @var array
     */
    protected $_typeMap = array(
        self::MAPPER_SORTING => 'Mage_Backend_Model_Config_Structure_Mapper_Sorting',
        self::MAPPER_PATH => 'Mage_Backend_Model_Config_Structure_Mapper_Path',
        self::MAPPER_IGNORE => 'Mage_Backend_Model_Config_Structure_Mapper_Ignore',
        self::MAPPER_DEPENDENCIES => 'Mage_Backend_Model_Config_Structure_Mapper_Dependencies',
        self::MAPPER_ATTRIBUTE_INHERITANCE => 'Mage_Backend_Model_Config_Structure_Mapper_Attribute_Inheritance',
        self::MAPPER_EXTENDS => 'Mage_Backend_Model_Config_Structure_Mapper_Extends',
    );

    public function __construct(Magento_ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Get mapper instance
     *
     * @param string $type
     * @param array $arguments
     * @return Mage_Backend_Model_Config_Structure_MapperInterface
     * @throws Exception
     */
    public function create($type)
    {
        $className = $this->_getMapperClassNameByType($type);

        /** @var Mage_Backend_Model_Config_Structure_MapperInterface $mapperInstance  */
        $mapperInstance =  $this->_objectManager->create($className);

        if (false == ($mapperInstance instanceof Mage_Backend_Model_Config_Structure_MapperInterface)) {
            throw new Exception('Mapper object is not instance on Mage_Backend_Model_Config_Structure_MapperInterface');
        }
        return $mapperInstance;
    }

    /**
     * Get mapper class name by type
     *
     * @param string $type
     * @return string mixed
     * @throws InvalidArgumentException
     */
    protected function _getMapperClassNameByType($type)
    {
        if (false == isset($this->_typeMap[$type])) {
            throw new InvalidArgumentException('Invalid mapper type: ' . $type);
        }
        return $this->_typeMap[$type];
    }
}
