<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Backend_Model_Config_Structure_Element_Iterator_Field
    extends Mage_Backend_Model_Config_Structure_Element_Iterator
{
    /**
     * Group flyweight
     *
     * @var Mage_Backend_Model_Config_Structure_Element_Group
     */
    protected $_groupFlyweight;

    /**
     * Field element flyweight
     *
     * @var Mage_Backend_Model_Config_Structure_Element_Field
     */
    protected $_fieldFlyweight;

    /**
     * @param Mage_Backend_Model_Config_Structure_Element_Group $groupFlyweight
     * @param Mage_Backend_Model_Config_Structure_Element_Field $fieldFlyweight
     */
    public function __construct(
        Mage_Backend_Model_Config_Structure_Element_Group $groupFlyweight,
        Mage_Backend_Model_Config_Structure_Element_Field $fieldFlyweight
    ) {
        $this->_groupFlyweight = $groupFlyweight;
        $this->_fieldFlyweight = $fieldFlyweight;
    }

    /**
     * Init current element
     *
     * @param array $element
     * @throws LogicException
     */
    protected function _initFlyweight(array $element)
    {
        if (!isset($element[Mage_Backend_Model_Config_Structure::TYPE_KEY])) {
            throw new LogicException('System config structure element must contain "type" attribute');
        }
        switch($element[Mage_Backend_Model_Config_Structure::TYPE_KEY]) {
            case 'group':
                $this->_flyweight = $this->_groupFlyweight;
                break;

            case 'field':
            default:
                $this->_flyweight = $this->_fieldFlyweight;
        }
        parent::_initFlyweight($element);
    }
}
