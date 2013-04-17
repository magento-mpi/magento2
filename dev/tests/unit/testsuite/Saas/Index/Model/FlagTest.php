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
        $this->_flagMock = $this->getMock('Saas_Index_Model_Flag', array('getState'), array(), '', false);
    }

    /**
     * @param int|null $state
     * @param bool $result
     * @dataProvider dataProviderForIsShowIndexNotification
     */
    public function testIsShowIndexNotification($state, $result)
    {
        $this->_flagMock->expects($this->once())->method('getState')
            ->will($this->returnValue($state));
        $this->assertEquals($result, $this->_flagMock->isShowIndexNotification());
    }

    /**
     * @param int $state
     * @param bool $result
     * @dataProvider dataProviderForIsTaskAdded
     */
    public function testIsTaskAdded($state, $result)
    {
        $this->_flagMock->expects($this->once())->method('getState')
            ->will($this->returnValue($state));
        $this->assertEquals($result, $this->_flagMock->isTaskAdded());
    }

    public function testIsTaskFinished()
    {
        $this->_flagMock->expects($this->once())->method('getState')
            ->will($this->returnValue(Saas_Index_Model_Flag::STATE_FINISHED));
        $this->assertTrue($this->_flagMock->isTaskFinished());
    }

    public function testIsTaskProcessing()
    {
        $this->_flagMock->expects($this->once())->method('getState')
            ->will($this->returnValue(Saas_Index_Model_Flag::STATE_PROCESSING));
        $this->assertTrue($this->_flagMock->isTaskProcessing());
    }

    public function testIsTaskNotified()
    {
        $this->_flagMock->expects($this->once())->method('getState')
            ->will($this->returnValue(Saas_Index_Model_Flag::STATE_NOTIFIED));
        $this->assertTrue($this->_flagMock->isTaskNotified());
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
}
