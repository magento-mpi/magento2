<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Module\Declaration\Converter;

class DomTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Module\Declaration\Converter\Dom
     */
    protected $_converter;

    protected function setUp()
    {
        $this->_converter = new \Magento\Module\Declaration\Converter\Dom();
    }

    public function testConvertWithValidDom()
    {
        $xmlFilePath = __DIR__ . '/_files/valid_module.xml';
        $dom = new \DOMDocument();
        $dom->loadXML(file_get_contents($xmlFilePath));
        $expectedResult = include __DIR__ . '/_files/converted_valid_module.php';
        $this->assertEquals($expectedResult, $this->_converter->convert($dom));
    }

    /**
     * @param string $xmlString
     * @dataProvider testConvertWithInvalidDomDataProvider
     * @expectedException \Exception
     */
    public function testConvertWithInvalidDom($xmlString)
    {
        $dom = new \DOMDocument();
        $dom->loadXML($xmlString);
        $this->_converter->convert($dom);
    }

    public function testConvertWithInvalidDomDataProvider()
    {
        return array(
            'Module node without "name" attribute' => array(
                '<?xml version="1.0"?><config><module /></config>'
            ),
            'Module node without "version" attribute' => array(
                '<?xml version="1.0"?><config><module name="Module_One" /></config>'
            ),
            'Module node without "active" attribute' => array(
                '<?xml version="1.0"?><config><module name="Module_One" version="1.0.0.0" /></config>'
            ),
            'Dependency module node without "name" attribute' => array(
                '<?xml version="1.0"?><config><module name="Module_One" version="1.0.0.0" active="true">'
                    . '<sequence><module/></sequence></module></config>'
            ),
            'Dependency extension node without "name" attribute' => array(
                '<?xml version="1.0"?><config><module name="Module_One" version="1.0.0.0" active="true">'
                . '<depends><extension/></depends></module></config>'
            ),
            'Empty choice node' => array(
                '<?xml version="1.0"?><config><module name="Module_One" version="1.0.0.0" active="true">'
                . '<depends><choice/></depends></module></config>'
            ),
        );
    }
}
