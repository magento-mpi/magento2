<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Backend_Model_Config_Structure_Element_Iterator_Field
    extends Magento_Backend_Model_Config_Structure_Element_Iterator
{
    /**
     * Group flyweight
     *
     * @var Magento_Backend_Model_Config_Structure_Element_Group
     */
    protected $_groupFlyweight;

    /**
     * Field element flyweight
     *
     * @var Magento_Backend_Model_Config_Structure_Element_Field
     */
    protected $_fieldFlyweight;

    /**
     * @param Magento_Backend_Model_Config_Structure_Element_Group $groupFlyweight
     * @param Magento_Backend_Model_Config_Structure_Element_Field $fieldFlyweight
     */
    public function __construct(
        Magento_Backend_Model_Config_Structure_Element_Group $groupFlyweight,
        Magento_Backend_Model_Config_Structure_Element_Field $fieldFlyweight
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
        if (!isset($element[Magento_Backend_Model_Config_Structure::TYPE_KEY])) {
            throw new LogicException('System config structure element must contain "type" attribute');
        }
        switch($element[Magento_Backend_Model_Config_Structure::TYPE_KEY]) {
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
