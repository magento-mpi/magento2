<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Backend_Model_Config_Structure_Element_FlyweightFactory
{
    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * Map of flyweight types
     *
     * @var array
     */
    protected $_flyweightMap = array(
        'section' => 'Mage_Backend_Model_Config_Structure_Element_Section',
        'group' => 'Mage_Backend_Model_Config_Structure_Element_Group',
        'field' => 'Mage_Backend_Model_Config_Structure_Element_Field'
    );

    /**
     * @param Magento_ObjectManager $objectManager
     */
    public function __construct(Magento_ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create element flyweight flyweight
     *
     * @param string $type
     * @return Mage_Backend_Model_Config_Structure_ElementInterface
     */
    public function create($type)
    {
        return $this->_objectManager->create($this->_flyweightMap[$type]);
    }
}
