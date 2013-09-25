<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Cron_Model_Config_DataTest extends PHPUnit_Framework_TestCase
{
    /**
     * Testing return jobs from different sources (DB, XML)
     */
    public function testGetJobs()
    {
        $reader = $this->getMockBuilder('Magento_Cron_Model_Config_Reader_Xml')
            ->disableOriginalConstructor()
            ->getMock();
        $cache = $this->getMock('Magento_Config_CacheInterface');
        $dbReader = $this->getMockBuilder('Magento_Cron_Model_Config_Reader_Db')
            ->disableOriginalConstructor()
            ->getMock();

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

        $cache->expects($this->any())
            ->method('load')
            ->with($this->equalTo('test_cache_id'))
            ->will($this->returnValue(serialize($jobs)));

        $dbReader->expects($this->once())->method('get')->will($this->returnValue($dbReaderData));

        $configData = new Magento_Cron_Model_Config_Data(
            $reader, $cache, $dbReader, 'test_cache_id'
        );

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

        $result = $configData->getJobs();
        $this->assertEquals($expected, $result);
    }
}
