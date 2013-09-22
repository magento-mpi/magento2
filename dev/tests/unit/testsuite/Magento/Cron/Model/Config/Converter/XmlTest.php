<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Cron
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Cron_Model_Config_Converter_XmlTest extends PHPUnit_Framework_TestCase
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
        $result = $this->_converter->convert(new DOMDocument());
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
            )
        );

        $xmlFile = __DIR__ . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'validCrontab.xml';
        $dom = new DOMDocument();
        $dom->loadXML(file_get_contents($xmlFile));
        $result = $this->_converter->convert($dom);

        $this->assertEquals($expected['job1']['schedule'], $result['job1']['schedule']);
        $this->assertEquals($expected['job1']['name'], $result['job1']['name']);
        $this->assertEquals($expected['job1']['instance'], $result['job1']['instance']);
        $this->assertEquals($expected['job1']['method'], $result['job1']['method']);
    }

    /**
     * Testing converting not valid cron configuration, expect to get exception
     *
     * @expectedException InvalidArgumentException
     */
    public function testConvertWrongConfiguration()
    {
        $xmlFile = __DIR__ . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'wrongCrontab.xml';
        $dom = new DOMDocument();
        $dom->loadXML(file_get_contents($xmlFile));
        $this->_converter->convert($dom);
    }
}
