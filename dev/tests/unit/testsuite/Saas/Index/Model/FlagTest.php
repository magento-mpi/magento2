<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Index_Model_FlagTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_flagMock;

    protected function setUp()
    {
        $this->_flagMock = $this->getMock('Saas_Index_Model_Flag', array('getState', 'setState', 'save'), array(), '',
            false);
    }

    /**
     * @param int|null $state
     * @param bool $result
     * @dataProvider dataProviderForIsShowIndexNotification
     */
    public function testIsShowIndexNotification($state, $result)
    {
        $this->_flagMock->expects($this->once())->method('getState')->will($this->returnValue($state));

        $this->assertEquals($result, $this->_flagMock->isShowIndexNotification());
    }

    /**
     * @return array
     */
    public function dataProviderForIsShowIndexNotification()
    {
        return array(
            array(Saas_Index_Model_Flag::STATE_NOTIFIED, true),
            array(null, true),
            array('unknown', false),
        );
    }

    /**
     * @param int $state
     * @param bool $result
     * @dataProvider dataProviderForIsTaskAdded
     */
    public function testIsTaskAdded($state, $result)
    {
        $this->_flagMock->expects($this->once())->method('getState')->will($this->returnValue($state));

        $this->assertEquals($result, $this->_flagMock->isTaskAdded());
    }

    /**
     * @return array
     */
    public function dataProviderForIsTaskAdded()
    {
        return array(
            array(Saas_Index_Model_Flag::STATE_PROCESSING, true),
            array(Saas_Index_Model_Flag::STATE_QUEUED, true),
            array('unknown', false),
        );
    }

    /**
     * @param string $method
     * @param string $status
     * @dataProvider dataProviderTaskStatusMethods
     */
    public function testTaskStatusMethods($method, $status)
    {
        $this->_flagMock->expects($this->once())->method('getState')
            ->will($this->returnValue($status));

        $this->assertTrue($this->_flagMock->$method());
    }

    public function dataProviderTaskStatusMethods()
    {
        return array(
            array('isTaskFinished', Saas_Index_Model_Flag::STATE_FINISHED),
            array('isTaskProcessing', Saas_Index_Model_Flag::STATE_PROCESSING),
            array('isTaskNotified', Saas_Index_Model_Flag::STATE_NOTIFIED),
        );
    }

    public function testSaveAsNotified()
    {
        $this->_flagMock->expects($this->once())->method('setState')->with(Saas_Index_Model_Flag::STATE_NOTIFIED)
            ->will($this->returnSelf());
        $this->_flagMock->expects($this->once())->method('save');

        $this->_flagMock->saveAsNotified();
    }
}
