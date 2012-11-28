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
     * List of element flyweights
     *
     * @var Mage_Backend_Model_Config_Structure_ElementInterface[]
     */
    protected $_flyweights;

    /**
     * @param Mage_Backend_Model_Config_Structure_Element_Section $sectionFlyweight
     * @param Mage_Backend_Model_Config_Structure_Element_Group $groupFlyweight
     * @param Mage_Backend_Model_Config_Structure_Element_Field $fieldFlyweight
     */
    public function __construct(
        Mage_Backend_Model_Config_Structure_Element_Section $sectionFlyweight,
        Mage_Backend_Model_Config_Structure_Element_Group $groupFlyweight,
        Mage_Backend_Model_Config_Structure_Element_Field $fieldFlyweight
    ) {
        $this->_flyweights = array(
            'section' => $sectionFlyweight,
            'group' => $groupFlyweight,
            'field' => $fieldFlyweight
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
        $flyweight = $this->_flyweights[$data['_elementType']];
        $flyweight->setData($data, $scope);
        return $flyweight;
    }
}
