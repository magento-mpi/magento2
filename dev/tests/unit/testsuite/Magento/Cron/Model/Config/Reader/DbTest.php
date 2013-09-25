<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Cron
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Cron\Model\Config\Reader;

class DbTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\Config\Section\Reader\DefaultReader|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_defaultReader;

    /**
     * @var \Magento\Cron\Model\Config\Converter\Db|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_converter;

    /**
     * @var \Magento\Cron\Model\Config\Reader\Db
     */
    protected $_reader;

    /**
     * Initialize parameters
     */
    protected function setUp()
    {
        $this->_defaultReader = $this->getMockBuilder('Magento\Core\Model\Config\Section\Reader\DefaultReader')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_converter = new \Magento\Cron\Model\Config\Converter\Db();
        $this->_reader = new \Magento\Cron\Model\Config\Reader\Db($this->_defaultReader, $this->_converter);
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
