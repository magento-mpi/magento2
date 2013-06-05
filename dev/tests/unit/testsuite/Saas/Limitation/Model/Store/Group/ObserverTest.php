<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Limitation_Model_Store_Group_ObserverTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Saas_Limitation_Model_Store_Group_Observer
     */
    private $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $_limitation;

    protected function setUp()
    {
        $this->_limitation = $this->getMock(
            'Saas_Limitation_Model_Store_Group_Limitation',
            array('isCreateRestricted', 'getCreateRestrictedMessage'),
            array(), '', false
        );
        $this->_model = new Saas_Limitation_Model_Store_Group_Observer($this->_limitation);
    }

    /**
     * @param bool $isLimitationReached
     * @param bool $isNewEntity
     * @dataProvider restrictEntityCreationInactiveDataProvider
     */
    public function testRestrictEntityCreationInactive($isLimitationReached, $isNewEntity)
    {
        $this->_limitation
            ->expects($this->any())->method('isCreateRestricted')->will($this->returnValue($isLimitationReached));

        $this->_limitation->expects($this->never())->method('getCreateRestrictedMessage');

        $entity = $this->getMock('Mage_Core_Model_Abstract', array('isObjectNew'), array(), '', false);
        $entity->expects($this->any())->method('isObjectNew')->will($this->returnValue($isNewEntity));

        $this->_model->restrictEntityCreation(new Varien_Event_Observer(array(
            'event' => new Varien_Object(array('data_object' => $entity))
        )));
    }

    public function restrictEntityCreationInactiveDataProvider()
    {
        return array(
            'limitation not reached & existing entity'  => array(false, false),
            'limitation not reached & new entity'       => array(false, true),
            'limitation reached & existing entity'      => array(true, false),
        );
    }

    /**
     * @expectedException Mage_Core_Exception
     * @expectedExceptionMessage Limitation has been reached
     */
    public function testRestrictEntityCreationActive()
    {
        $this->_limitation
            ->expects($this->any())->method('isCreateRestricted')->will($this->returnValue(true));

        $this->_limitation
            ->expects($this->once())
            ->method('getCreateRestrictedMessage')
            ->will($this->returnValue('Limitation has been reached'))
        ;

        $entity = $this->getMock('Mage_Core_Model_Abstract', array('isObjectNew'), array(), '', false);
        $entity->expects($this->any())->method('isObjectNew')->will($this->returnValue(true));

        $this->_model->restrictEntityCreation(new Varien_Event_Observer(array(
            'event' => new Varien_Object(array('data_object' => $entity))
        )));
    }

    /**
     * @param bool $isLimitationReached
     * @param string $blockClass
     * @dataProvider removeCreationButtonInactiveDataProvider
     */
    public function testRemoveCreationButtonInactive($isLimitationReached, $blockClass)
    {
        $this->_limitation
            ->expects($this->any())->method('isCreateRestricted')->will($this->returnValue($isLimitationReached));

        $block = $this->getMock($blockClass, array('updateButton'), array(), '', false);
        $block->expects($this->never())->method('updateButton');

        $this->_model->removeCreationButton(new Varien_Event_Observer(array(
            'event' => new Varien_Object(array('block' => $block))
        )));
    }

    public function removeCreationButtonInactiveDataProvider()
    {
        return array(
            'limitation not reached & relevant grid'    => array(false, 'Mage_Adminhtml_Block_System_Store_Store'),
            'limitation reached & irrelevant grid'      => array(true, 'Mage_Backend_Block_Widget_Grid_Container'),
        );
    }

    public function testRemoveCreationButtonActive()
    {
        $this->_limitation
            ->expects($this->once())->method('isCreateRestricted')->will($this->returnValue(true));

        $block = $this->getMock('Mage_Adminhtml_Block_System_Store_Store', array('removeButton'), array(), '', false);
        $block->expects($this->once())->method('removeButton')->with('add_group');

        $this->_model->removeCreationButton(new Varien_Event_Observer(array(
            'event' => new Varien_Object(array('block' => $block))
        )));
    }
}
