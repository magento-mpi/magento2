<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Cron
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Cron\Model\Config\Converter;

class XmlTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Cron\Model\Config\Converter\Xml
     */
    protected $_converter;

    /**
     * Initialize parameters
     */
    protected function setUp()
    {
        $this->_converter = new \Magento\Cron\Model\Config\Converter\Xml();
    }

    /**
     * Testing wrong data incoming
     */
    public function testConvertWrongIncomingData()
    {
        $result = $this->_converter->convert(array('wrong data'));
        $this->assertEmpty($result);
    }

    /**
     * Testing not existing of node <job>
     */
    public function testConvertNoElements()
    {
        $result = $this->_converter->convert(new \DOMDocument());
        $this->assertEmpty($result);
    }

    /**
     * Testing converting valid cron configuration
     */
    public function testConvert()
    {
        $expected = array(
            'job1' => array(
                'name' => 'job1',
                'schedule' => '30 2 * * *',
                'instance' => 'Model1',
                'method' => 'method1'
            ),
            'job2' => array(
                'name' => 'job2',
                'schedule' => '* * * * *',
                'instance' => 'Model2',
                'method' => 'method2'
            ),
        );

        $xmlFile = __DIR__ . '/../_files/crontab_valid.xml';
        $dom = new \DOMDocument();
        $dom->loadXML(file_get_contents($xmlFile));
        $result = $this->_converter->convert($dom);

        $actual = array(
            'job1' => array(
                'name' => $result['job1']['name'],
                'schedule' => $result['job1']['schedule'],
                'instance' => $result['job1']['instance'],
                'method' => $result['job1']['method']
            ),
            'job2' => array(
                'name' => $result['job2']['name'],
                'schedule' => $result['job2']['schedule'],
                'instance' => $result['job2']['instance'],
                'method' => $result['job2']['method']
            ),
        );

        $this->assertEquals($expected, $actual);
    }

    /**
     * Testing converting not valid cron configuration, expect to get exception
     *
     * @expectedException \InvalidArgumentException
     */
    public function testConvertWrongConfiguration()
    {
        $xmlFile = __DIR__ . '/../_files/crontab_invalid.xml';
        $dom = new \DOMDocument();
        $dom->loadXML(file_get_contents($xmlFile));
        $this->_converter->convert($dom);
    }
}
