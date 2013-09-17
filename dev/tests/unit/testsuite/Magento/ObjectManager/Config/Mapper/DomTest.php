<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
class Magento_ObjectManager_Config_Mapper_DomTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_ObjectManager_Config_Mapper_Dom
     */
    protected $_mapper;

    protected function setUp()
    {
        $this->_mapper = new Magento_ObjectManager_Config_Mapper_Dom();
    }

    public function testConvert()
    {
        $dom = new DOMDocument();
        $xmlFile = __DIR__ . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'simple_di_config.xml';
        $dom->loadXML(file_get_contents($xmlFile));

        $resultFile = __DIR__ . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'mapped_simple_di_config.php';
        $expectedResult = include $resultFile;
        $this->assertEquals($expectedResult, $this->_mapper->convert($dom));
    }

    /**
     * @param string $xmlData
     * @dataProvider wrongXmlDataProvider
     * @expectedException Exception
     * @expectedExceptionMessage Invalid application config. Unknown node: wrong_node.
     */
    public function testMapThrowsExceptionWhenXmlHasWrongFormat($xmlData)
    {
        $dom = new DOMDocument();
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
                '<?xml version="1.0"?><config><type name="some_type">'
                    . '<param name="some_param"><wrong_node name="wrong_node" /></param>'
                    . '</type></config>',
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
