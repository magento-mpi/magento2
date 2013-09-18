<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Cron
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Cron_Model_Config_Reader_DbTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Core_Model_Config_Section_Reader_DefaultReader|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_defaultReader;

    /**
     * @var Magento_Cron_Model_Config_Converter_Db|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_converter;

    /**
     * @var Magento_Cron_Model_Config_Reader_Db
     */
    protected $_reader;

    /**
     * Initialize parameters
     */
    protected function setUp()
    {
        $this->_defaultReader = $this->getMockBuilder('Magento_Core_Model_Config_Section_Reader_DefaultReader')
            ->disableOriginalConstructor()
            ->setMethods(array('read'))
            ->getMock();
        $this->_converter = new Magento_Cron_Model_Config_Converter_Db();

        $this->_reader = new Magento_Cron_Model_Config_Reader_Db($this->_defaultReader, $this->_converter);
    }

    /**
     * Testing method execution
     */
    public function testGet()
    {
        $job1 = array(
            'schedule' => array('cron_expr' => '* * * * *')
        );
        $job2 = array(
            'schedule' => array('cron_expr' => '1 1 1 1 1')
        );
        $data = array(
            'crontab' => array('jobs' => array('job1' => $job1, 'job2' => $job2))
        );
        $this->_defaultReader->expects($this->once())->method('read')->will($this->returnValue($data));
        $expected = array(
            'job1' => array('schedule' => $job1['schedule']['cron_expr']),
            'job2' => array('schedule' => $job2['schedule']['cron_expr'])
        );

        $result = $this->_reader->get();
        $this->assertEquals($expected['job1']['schedule'], $result['job1']['schedule']);
        $this->assertEquals($expected['job2']['schedule'], $result['job2']['schedule']);
    }
}
