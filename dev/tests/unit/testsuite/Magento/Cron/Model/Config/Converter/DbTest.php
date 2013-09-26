<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Cron
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Cron_Model_Config_Converter_DbTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Cron_Model_Config_Converter_Db
     */
    protected $_converter;

    /**
     * Prepare parameters
     */
    protected function setUp()
    {
        $this->_converter = new Magento_Cron_Model_Config_Converter_Db();
    }

    /**
     * Testing not existed list of jobs
     */
    public function testConvertNoJobs()
    {
        $source = array();
        $result = $this->_converter->convert($source);
        $this->assertEmpty($result);
    }

    /**
     * Testing parameters in 'schedule' container
     */
    public function testConvertConfigParams()
    {
        $fullJob = array(
            'schedule' => array(
                'config_path' => 'config/path',
                'cron_expr'   => '* * * * *'
            )
        );
        $nullJob = array(
            'schedule' => array(
                'config_path' => null,
                'cron_expr' => null
            )
        );
        $notFullJob = array(
            'schedule' => ''
        );
        $source = array(
            'crontab' => array(
                'jobs' => array(
                    'job_name_1' => $fullJob,
                    'job_name_2' => $nullJob,
                    'job_name_3' => $notFullJob,
                    'job_name_4' => array()
                )
            )
        );
        $expected = array(
            'job_name_1' => array('config_path' => 'config/path', 'schedule' => '* * * * *'),
            'job_name_2' => array('config_path' => null, 'schedule' => null),
            'job_name_3' => array('schedule' => ''),
            'job_name_4' => array(''),
        );

        $result = $this->_converter->convert($source);
        $this->assertEquals($expected['job_name_1']['config_path'], $result['job_name_1']['config_path']);
        $this->assertEquals($expected['job_name_1']['schedule'], $result['job_name_1']['schedule']);

        $this->assertEquals($expected['job_name_2']['config_path'], $result['job_name_2']['config_path']);
        $this->assertEquals($expected['job_name_2']['schedule'], $result['job_name_2']['schedule']);

        $this->assertArrayHasKey('schedule', $result['job_name_3']);
        $this->assertEmpty($result['job_name_3']['schedule']);

        $this->assertEmpty($result['job_name_4']);
    }

    /**
     * Testing 'run' container
     */
    public function testConvertRunConfig()
    {
        $runFullJob = array(
            'run' => array('model' => 'Model1::method1')
        );
        $runNoMethodJob = array(
            'run' => array('model' => 'Model2')
        );
        $runEmptyMethodJob = array(
            'run' => array('model' => 'Model3::')
        );
        $runNoModelJob = array(
            'run' => array('model' => '::method1')
        );

        $source = array(
            'crontab' => array(
                'jobs' => array(
                    'job_name_1' => $runFullJob,
                    'job_name_2' => $runNoMethodJob,
                    'job_name_3' => $runEmptyMethodJob,
                    'job_name_4' => $runNoModelJob,
                )
            )
        );
        $expected = array(
            'job_name_1' => array('instance' => 'Model1', 'method' => 'method1'),
            'job_name_2' => array(),
            'job_name_3' => array(),
            'job_name_4' => array()
        );
        $result = $this->_converter->convert($source);
        $this->assertEquals($expected['job_name_1']['instance'], $result['job_name_1']['instance']);
        $this->assertEquals($expected['job_name_1']['method'], $result['job_name_1']['method']);

        $this->assertEmpty($result['job_name_2']);
        $this->assertEmpty($result['job_name_3']);
        $this->assertEmpty($result['job_name_4']);
    }
}
