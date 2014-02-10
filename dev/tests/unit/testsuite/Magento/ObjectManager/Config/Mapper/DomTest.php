<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\ObjectManager\Config\Mapper;

class DomTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\ObjectManager\Config\Mapper\Dom
     */
    protected $_mapper;

    protected function setUp()
    {
        $callback = function ($argument) {
            /** @var $argument \DOMElement */
            if ($argument->getAttribute('name') == 'test name'
                && $argument->getAttribute('xsi:type') == 'test_type'
                && $argument->nodeValue == 'test value'
            ) {
                return array(
                    'name' => 'test name',
                    'xsi:type' => 'test_type',
                    'value' => 'test value'
                );
            }
        };

        $argumentParserMock = $this->getMock('\Magento\ObjectManager\Config\Mapper\ArgumentParser');
        $argumentParserMock->expects($this->any())
            ->method('parse')
            ->will($this->returnCallback($callback));
        $this->_mapper = new \Magento\ObjectManager\Config\Mapper\Dom($argumentParserMock);
    }

    public function testConvert()
    {
        $dom = new \DOMDocument();
        $xmlFile = __DIR__ . '/_files/simple_di_config.xml';
        $dom->loadXML(file_get_contents($xmlFile));

        $resultFile = __DIR__ . '/_files/mapped_simple_di_config.php';
        $expectedResult = include $resultFile;
        $this->assertEquals($expectedResult, $this->_mapper->convert($dom));
    }

    /**
     * @param string $xmlData
     * @dataProvider wrongXmlDataProvider
     * @expectedException \Exception
     * @expectedExceptionMessage Invalid application config. Unknown node: wrong_node.
     */
    public function testMapThrowsExceptionWhenXmlHasWrongFormat($xmlData)
    {
        $dom = new \DOMDocument();
        $dom->loadXML($xmlData);
        $this->_mapper->convert($dom);
    }

    /**
     * @return array
     */
    public function wrongXmlDataProvider()
    {
        return array(
            array(
                '<?xml version="1.0"?><config><type name="some_type">'
                    . '<wrong_node name="wrong_node" />'
                    . '</type></config>',
            ),
            array(
                '<?xml version="1.0"?><config><virtualType name="some_type">'
                    . '<wrong_node name="wrong_node" />'
                    . '</virtualType></config>',
            ),
            array(
                '<?xml version="1.0"?><config>'
                    . '<preference for="some_interface" type="some_class" />'
                    . '<wrong_node name="wrong_node" />'
                    . '</config>',
            ),
        );
    }
}
