<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Acl_Resource_Config_Converter_DomTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Acl\Resource\Config\Converter\Dom
     */
    protected $_converter;

    protected function setUp()
    {
        $this->_converter = new \Magento\Acl\Resource\Config\Converter\Dom();
    }

    /**
     * @param array $expectedResult
     * @param string $xml
     * @dataProvider convertWithValidDomDataProvider
     */
    public function testConvertWithValidDom(array $expectedResult, $xml)
    {
        $dom = new DOMDocument();
        $dom->loadXML($xml);
        $this->assertEquals($expectedResult, $this->_converter->convert($dom));
    }

    /**
     * @return array
     */
    public function convertWithValidDomDataProvider()
    {
        return array(
            array(
                include __DIR__ . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'converted_valid_acl.php',
                file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'valid_acl.xml'),
            ),
        );
    }

    /**
     * @param string $xml
     * @expectedException Exception
     * @dataProvider convertWithInvalidDomDataProvider
     */
    public function testConvertWithInvalidDom($xml)
    {
        $dom = new DOMDocument();
        $dom->loadXML($xml);
        $this->_converter->convert($dom);
    }

    /**
     * @return array
     */
    public function convertWithInvalidDomDataProvider()
    {
        return array(
            array(
                'resource without "id" attribute' => '<?xml version="1.0"?><config><acl>'
                    . '<resources><resource/></resources></acl></config>',
            ),
        );
    }
}
