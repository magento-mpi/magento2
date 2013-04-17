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
        $helper = new Magento_Test_Helper_ObjectManager($this);
        $factoryMock = $this->getMock('Saas_Index_Model_FlagFactory', array('create'), array(), '', false);
        $this->_flagMock = $this->getMock('Saas_Index_Model_Flag',
            array('getState', 'setState', 'save', 'loadSelf', 'isTaskFinished'),
            array(), '', false
        );
        $this->_flagMock->expects($this->once())->method('loadSelf');
        $factoryMock->expects($this->any())->method('create')->will($this->returnValue($this->_flagMock));

        $this->_helperMock = $this->getMock('Saas_Index_Helper_Data', array(), array(), '', false);
        $arguments = array(
            'flagFactory' => $factoryMock,
            'helper' => $this->_helperMock,
        );
        $this->_model = $helper->getObject('Saas_Index_Model_System_Message_IndexRefreshed', $arguments);
    }

    public function testGetIdentity()
    {
        $this->assertEquals(Saas_Index_Model_System_Message_IndexRefreshed::MESSAGE_IDENTITY, $this->_model->getIdentity());
    }

    public function testIsDisplayedWithNotFinishedState()
    {
        $this->_flagMock->expects($this->any())
            ->method('isTaskFinished')
            ->will($this->returnValue(false));
        $this->_flagMock->expects($this->never())->method('save');
        $this->assertFalse($this->_model->isDisplayed());
    }

    public function testIsDisplayedWithFinishedState()
    {
        $this->_flagMock->expects($this->any())
            ->method('isTaskFinished')
            ->will($this->returnValue(true));
        $this->_flagMock->expects($this->once())->method('setState')->with(Saas_Index_Model_Flag::STATE_NOTIFIED);
        $this->_flagMock->expects($this->once())->method('save');
        $this->assertTrue($this->_model->isDisplayed());

        /** check internal cache */
        $this->assertTrue($this->_model->isDisplayed());
    }

    public function testGetText()
    {
        $this->_helperMock->expects($this->any())->method('__')->will($this->returnArgument(0));
        $this->assertEquals('Search index has been refreshed', $this->_model->getText());
    }

    public function testGetSeverity()
    {
        $this->assertEquals(
            Saas_Index_Model_System_Message_IndexRefreshed::SEVERITY_MAJOR,
            $this->_model->getSeverity()
        );
    }
}