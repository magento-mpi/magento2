<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_ImportExport_Model_System_Message_ExportFinishedTest extends PHPUnit_Framework_TestCase {
    /**
     * @var Saas_ImportExport_Model_System_Message_ExportFinished
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_stateHelperMock;

    protected function setUp()
    {
        $this->_stateHelperMock = $this->getMock('Saas_ImportExport_Helper_Export_State', array(), array(), '', false);
        $objectManager = new Magento_Test_Helper_ObjectManager($this);
        $this->_model = $objectManager->getObject('Saas_ImportExport_Model_System_Message_ExportFinished', array(
            'stateHelper' => $this->_stateHelperMock
        ));
    }

    public function testIsDisplayedWithNotFinishedState()
    {
        $this->_stateHelperMock->expects($this->any())->method('isTaskFinished')->will($this->returnValue(false));
        $this->_stateHelperMock->expects($this->never())->method('setTaskAsNotified');

        $this->assertFalse($this->_model->isDisplayed());
    }

    public function testIsDisplayedWithFinishedState()
    {
        $this->_stateHelperMock->expects($this->any())->method('isTaskFinished')->will($this->returnValue(true));
        $this->_stateHelperMock->expects($this->once())->method('setTaskAsNotified');

        $this->assertTrue($this->_model->isDisplayed());
        /** check internal cache */
        $this->assertTrue($this->_model->isDisplayed());
    }

    public function testGetText()
    {
        $translatedMessage = 'translated-message';
        $this->_stateHelperMock->expects($this->once())->method('__')->with('The Export task has been finished.')
            ->will($this->returnValue($translatedMessage));

        $this->assertEquals($translatedMessage, $this->_model->getText());
    }

    public function testGetIdentity()
    {
        $this->assertEquals(Saas_ImportExport_Model_System_Message_ExportFinished::MESSAGE_IDENTITY,
            $this->_model->getIdentity());
    }

    public function testGetSeverity()
    {
        $this->assertEquals(
            Mage_AdminNotification_Model_System_MessageInterface::SEVERITY_MAJOR,
            $this->_model->getSeverity()
        );
    }
}
