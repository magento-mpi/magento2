<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Cron
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Class Magento_Cron_Model_Config
 */
class Magento_Cron_Model_ConfigTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Cron_Model_Config_Data|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_configData;

    /**
     * @var Magento_Cron_Model_Config
     */
    protected $_config;

    /**
     * Prepare data
     */
    protected function setUp()
    {
        $this->_configData = $this->getMockBuilder('Magento_Cron_Model_Config_Data')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_config = new Magento_Cron_Model_Config($this->_configData);
    }

    /**
     * Test method call
     */
    public function testGetJobs()
    {
        $jobList = array(
            'jobname1' => array(
                'instance' => 'TestInstance',
                'method' => 'testMethod',
                'schedule' => '* * * * *'
            )
        );
        $this->_configData->expects($this->once())->method('getJobs')->will($this->returnValue($jobList));
        $result = $this->_config->getJobs();
        $this->assertEquals($jobList, $result);
    }
}
