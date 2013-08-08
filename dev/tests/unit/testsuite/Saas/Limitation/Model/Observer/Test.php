<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Limitation_Model_Observer_Test extends PHPUnit_Framework_TestCase
{
    /**
     * @var Saas_Limitation_Model_Observer_Controller
     */
    private $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $_session;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $_limitationValidator;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $_limitation;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $_dictionary;

    protected function setUp()
    {
        $this->_session = $this->getMock('Mage_Backend_Model_Session', array('addNotice'), array(), '', false);
        $this->_limitationValidator = $this->getMock(
            'Saas_Limitation_Model_Limitation_Validator', array('exceedsThreshold'), array(), '', false
        );
        $this->_limitation = $this->getMockForAbstractClass('Saas_Limitation_Model_Limitation_LimitationInterface');
        $this->_dictionary = $this->getMock(
            'Saas_Limitation_Model_Dictionary', array('getMessage'), array(), '', false
        );
        $this->_dictionary
            ->expects($this->once())
            ->method('getMessage')
            ->with('fixture_message')
            ->will($this->returnValue('Fixture Message Text'))
        ;
        $this->_model = new Saas_Limitation_Model_Observer_Controller(
            $this->_session,
            $this->_limitationValidator,
            $this->_limitation,
            $this->_dictionary,
            'fixture_message'
        );
    }

    public function testDisplayNotificationActive()
    {
        $this->_limitationValidator
            ->expects($this->once())
            ->method('exceedsThreshold')
            ->with($this->_limitation)
            ->will($this->returnValue(true))
        ;
        $this->_session->expects($this->once())->method('addNotice')->with('Fixture Message Text');
        $this->_model->displayNotification(new Magento_Event_Observer);
    }

    public function testDisplayNotificationInactive()
    {
        $this->_limitationValidator
            ->expects($this->once())
            ->method('exceedsThreshold')
            ->with($this->_limitation)
            ->will($this->returnValue(false))
        ;
        $this->_session->expects($this->never())->method('addNotice');
        $this->_model->displayNotification(new Magento_Event_Observer);
    }
}
