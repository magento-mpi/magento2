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

        $configData = array(/** TODO: Implement logic here */);
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
        $this->markTestIncomplete('Need to be implemented in scope of MAGETWO-9658');

        //TODO: Implement logic here

        $this->assertEquals($expected, $this->_model->isNotificationAllowed($jobName));
    }

    /**
     * @return array
     */
    public function isNotificationAllowedDataProvider()
    {
        return array(
            array(/** TODO: Implement logic here */),
            array(/** TODO: Implement logic here */),
            array(/** TODO: Implement logic here */),
        );
    }

    /**
     * @param string $jobName
     * @param string $expected
     * @dataProvider getJobTitleDataProvider
     */
    public function testGetJobTitle($jobName, $expected)
    {
        $this->markTestIncomplete('Need to be implemented in scope of MAGETWO-9658');

        //TODO: Implement logic here
        $this->assertEquals($expected, $this->_model->getJobTitle($jobName));
    }

    /**
     * @return array
     */
    public function getJobTitleDataProvider()
    {
        return array(
            array(/** TODO: Implement logic here */),
            array(/** TODO: Implement logic here */),
            array(/** TODO: Implement logic here */),
        );
    }
}