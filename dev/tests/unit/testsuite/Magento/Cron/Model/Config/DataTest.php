<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Cron\Model\Config;

class DataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Testing return jobs from different sources (DB, XML)
     */
    public function testGetJobs()
    {
        $reader = $this->getMockBuilder(
            'Magento\Cron\Model\Config\Reader\Xml'
        )->disableOriginalConstructor()->getMock();
        $cache = $this->getMock('Magento\Framework\Config\CacheInterface');
        $dbReader = $this->getMockBuilder(
            'Magento\Cron\Model\Config\Reader\Db'
        )->disableOriginalConstructor()->getMock();

        $jobs = array(
            'job1' => array('schedule' => '1 1 1 1 1', 'instance' => 'JobModel1_1', 'method' => 'method1_1'),
            'job3' => array('schedule' => '3 3 3 3 3', 'instance' => 'JobModel3', 'method' => 'method3')
        );

        $dbReaderData = array(
            'job1' => array('schedule' => '* * * * *', 'instance' => 'JobModel1', 'method' => 'method1'),
            'job2' => array('schedule' => '* * * * *', 'instance' => 'JobModel2', 'method' => 'method2')
        );

        $cache->expects(
            $this->any()
        )->method(
            'load'
        )->with(
            $this->equalTo('test_cache_id')
        )->will(
            $this->returnValue(serialize($jobs))
        );

        $dbReader->expects($this->once())->method('get')->will($this->returnValue($dbReaderData));

        $configData = new \Magento\Cron\Model\Config\Data($reader, $cache, $dbReader, 'test_cache_id');

        $expected = array(
            'job1' => array('schedule' => '* * * * *', 'instance' => 'JobModel1', 'method' => 'method1'),
            'job2' => array('schedule' => '* * * * *', 'instance' => 'JobModel2', 'method' => 'method2'),
            'job3' => array('schedule' => '3 3 3 3 3', 'instance' => 'JobModel3', 'method' => 'method3')
        );

        $result = $configData->getJobs();
        $this->assertEquals($expected, $result);
    }
}
