<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Cron
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Cron_Model_Config_DataTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Cron_Model_Config_Reader_Xml|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_reader;

    /**
     * @var Magento_Core_Model_Config_Scope|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_scope;

    /**
     * @var Magento_Core_Model_Cache_Type_Config|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_cache;

    /**
     * @var Magento_Cron_Model_Config_Reader_Db|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_dbReader;

    /**
     * @var Magento_Cron_Model_Config_Data
     */
    protected $_configData;

    /**
     * @var string
     */
    protected $_cacheId = 'test_cache_id';

    /**
     * Initialize parameters
     */
    protected function setUp()
    {
        $this->_reader = $this->getMockBuilder('Magento_Cron_Model_Config_Reader_Xml')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_scope = $this->getMockBuilder('Magento_Core_Model_Config_Scope')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_cache = $this->getMockBuilder('Magento_Core_Model_Cache_Type_Config')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_dbReader = $this->getMockBuilder('Magento_Cron_Model_Config_Reader_Db')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_configData = new Magento_Cron_Model_Config_Data(
            $this->_reader, $this->_scope, $this->_cache, $this->_dbReader, $this->_cacheId
        );
    }

    /**
     * Testing return jobs from different sources (DB, XML)
     */
    public function testGetJobs()
    {
        $dbReaderData = array(
            'job1' => array(
                'schedule' => '* * * * *',
                'instance' => 'JobModel1',
                'method'   => 'method1'
            ),
            'job2' => array(
                'schedule' => '* * * * *',
                'instance' => 'JobModel2',
                'method'   => 'method2'
            ),
        );
        $jobs = array(
            'job1' => array(
                'schedule' => '1 1 1 1 1',
                'instance' => 'JobModel1_1',
                'method' => 'method1_1'
            ),
            'job3' => array(
                'schedule' => '3 3 3 3 3',
                'instance' => 'JobModel3',
                'method' => 'method3'
            ),
        );
        $scope = 'global';
        $expected = array(
            'job1' => array(
                'schedule' => '* * * * *',
                'instance' => 'JobModel1',
                'method'   => 'method1'
            ),
            'job2' => array(
                'schedule' => '* * * * *',
                'instance' => 'JobModel2',
                'method'   => 'method2'
            ),
            'job3' => array(
                'schedule' => '3 3 3 3 3',
                'instance' => 'JobModel3',
                'method' => 'method3'
            ),
        );

        $this->_dbReader->expects($this->once())->method('get')->will($this->returnValue($dbReaderData));
        $this->_scope->expects($this->once())->method('getCurrentScope')->will($this->returnValue($scope));
        $this->_cache->expects($this->once())
            ->method('get')
            ->with($this->equalTo($scope), $this->equalTo($this->_cacheId))
            ->will($this->returnValue($jobs));

        $result = $this->_configData->getJobs();
        $this->assertEquals($expected, $result);
    }
}
