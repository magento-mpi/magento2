<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Backend
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Backend_Model_Config_Structure_Element_FlyweightPoolTest extends PHPUnit_Framework_TestCase
{
    public function testGetFlyweightReturnsInitializedFlyweight()
    {
        $sectionMock = $this->getMock(
            'Mage_Backend_Model_Config_Structure_Element_Section', array(), array(), '', false
        );
        $groupMock = $this->getMock(
            'Mage_Backend_Model_Config_Structure_Element_Group', array(), array(), '', false
        );
        $fieldMock = $this->getMock(
            'Mage_Backend_Model_Config_Structure_Element_Field', array(), array(), '', false
        );

        $model = new Mage_Backend_Model_Config_Structure_Element_FlyweightPool($sectionMock, $groupMock, $fieldMock);
        $groupMock->expects($this->once())->method('setData')->with(array('type' => 'group'));
        $this->assertEquals($groupMock, $model->getFlyweight(array('type' => 'group')));
    }
}
