<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Backend_Model_Config_Structure_Element_FlyweightPool
{
    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @param Magento_ObjectManager $objectManager
     */
    public function __construct(Magento_ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
        $this->_flyweights = array(
            'section' => 'Mage_Backend_Model_Config_Structure_Element_Section',
            'group' => 'Mage_Backend_Model_Config_Structure_Element_Group',
            'field' => 'Mage_Backend_Model_Config_Structure_Element_Field'
        );
    }

    /**
     * Retrieve initialized flyweight
     *
     * @param array $data
     * @param string $scope
     * @return Mage_Backend_Model_Config_Structure_ElementInterface
     */
    public function getFlyweight(array $data, $scope)
    {
        $flyweight = $this->_objectManager->create($this->_flyweights[$data['_elementType']]);
        $flyweight->setData($data, $scope);
        return $flyweight;
    }
}
