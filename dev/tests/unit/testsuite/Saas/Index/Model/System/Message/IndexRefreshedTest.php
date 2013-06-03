<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Index_Model_System_Message_IndexRefreshedTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Saas_Index_Model_System_Message_IndexRefreshed
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_flagMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_helperMock;

    protected function setUp()
    {
        $this->_flagMock = $this->getMock('Saas_Index_Model_Flag', array(), array(), '', false);
        $this->_flagMock->expects($this->once())->method('loadSelf')->will($this->returnSelf());
        $factoryMock = $this->getMock('Saas_Index_Model_FlagFactory', array('create'), array(), '', false);
        $factoryMock->expects($this->any())->method('create')->will($this->returnValue($this->_flagMock));

        $this->_helperMock = $this->getMock('Saas_Index_Helper_Data', array(), array(), '', false);
        $objectManager = new Magento_Test_Helper_ObjectManager($this);
        $this->_model = $objectManager->getObject('Saas_Index_Model_System_Message_IndexRefreshed', array(
            'flagFactory' => $factoryMock,
            'helper' => $this->_helperMock,
        ));
    }

    public function testGetIdentity()
    {
        $this->assertEquals(Saas_Index_Model_System_Message_IndexRefreshed::MESSAGE_IDENTITY,
            $this->_model->getIdentity());
    }

    public function testIsDisplayedWithNotFinishedState()
    {
        $this->_flagMock->expects($this->any())->method('isTaskFinished')->will($this->returnValue(false));
        $this->_flagMock->expects($this->never())->method('saveAsNotified');

        $this->assertFalse($this->_model->isDisplayed());
    }

    public function testIsDisplayedWithFinishedState()
    {
        $this->_flagMock->expects($this->any())->method('isTaskFinished')->will($this->returnValue(true));
        $this->_flagMock->expects($this->once())->method('saveAsNotified');

        $this->assertTrue($this->_model->isDisplayed());
        /** check internal cache */
        $this->assertTrue($this->_model->isDisplayed());
    }

    public function testGetText()
    {
        $translatedMessage = 'translated-message';
        $this->_helperMock->expects($this->once())->method('__')->with('Search index has been refreshed')
            ->will($this->returnValue($translatedMessage));

        $this->assertEquals($translatedMessage, $this->_model->getText());
    }

    public function testGetSeverity()
    {
        $this->assertEquals(
            Saas_Index_Model_System_Message_IndexRefreshed::SEVERITY_MAJOR,
            $this->_model->getSeverity()
        );
    }
}
