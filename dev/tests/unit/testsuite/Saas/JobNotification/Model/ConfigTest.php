<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Saas_JobNotification_Model_ConfigTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Saas_JobNotification_Model_Config
     */
    protected $_model;

    protected function setUp()
    {
        $configMock = $this->getMock('Mage_Core_Model_ConfigInterface');
        $nodeMock = $this->getMock('stdClass', array('asArray'), array(), '', false);

        $configMock->expects($this->once())
            ->method('getNode')
            ->with('global/tasks')
            ->will($this->returnValue($nodeMock));

        $configData = array(
            'refresh_catalog' => array('notification' => array('enabled' => true, 'title' => 'Refresh catalog')),
            'clear_css' => array('notification' => array('enabled' => false, 'title' => 'Clear CSS'))
        );
        $nodeMock->expects($this->once())->method('asArray')->will($this->returnValue($configData));

        $this->_model = new Saas_JobNotification_Model_Config($configMock);
    }

    /**
     * @param string $jobName
     * @param string $expected
     * @dataProvider isNotificationAllowedDataProvider
     */
    public function testIsNotificationAllowed($jobName, $expected)
    {
        $this->assertEquals($expected, $this->_model->isNotificationAllowed($jobName));
    }

    /**
     * @return array
     */
    public function isNotificationAllowedDataProvider()
    {
        return array(
            array('refresh_catalog', true),
            array('clear_css', false),
            array('unknown', false),
        );
    }

    /**
     * @param string $jobName
     * @param string $expected
     * @dataProvider getJobTitleDataProvider
     */
    public function testGetJobTitle($jobName, $expected)
    {
        $this->assertEquals($expected, $this->_model->getJobTitle($jobName));
    }

    /**
     * @return array
     */
    public function getJobTitleDataProvider()
    {
        return array(
            array('refresh_catalog', 'Refresh catalog'),
            array('clear_css', 'Clear CSS'),
            array('unknown', ''),
        );
    }
}