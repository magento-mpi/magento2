<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Framework
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Config_DomTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param string $xmlFile
     * @param string $newXmlFile
     * @param array $ids
     * @param string $expectedXmlFile
     * @dataProvider mergeDataProvider
     */
    public function testMerge($xmlFile, $newXmlFile, $ids, $expectedXmlFile)
    {
        $xml = file_get_contents(__DIR__ . "/_files/dom/{$xmlFile}");
        $newXml = file_get_contents(__DIR__ . "/_files/dom/{$newXmlFile}");
        $expectedXml = file_get_contents(__DIR__ . "/_files/dom/{$expectedXmlFile}");
        $config = new Magento_Config_Dom($xml, $ids);
        $config->merge($newXml);
        $this->assertXmlStringEqualsXmlString($expectedXml, $config->getDom()->saveXML());
    }

    /**
     * @return array
     */
    public function mergeDataProvider()
    {
        // note differences of XML declaration in fixture files: sometimes encoding is specified, sometimes isn't
        return array(
            array('ids.xml', 'ids_new.xml', array(
                    '/root/node/subnode'     => 'id',
                    '/root/other_node'       => 'id',
                    '/root/other_node/child' => 'identifier',
                ),
                'ids_merged.xml'
            ),
            array('no_ids.xml', 'no_ids_new.xml', array(), 'no_ids_merged.xml'),
            array('ambiguous_one.xml', 'ambiguous_new_two.xml', array(), 'ambiguous_merged.xml'),
            array('namespaced.xml', 'namespaced_new.xml', array(
                '/root/node'     => 'id',
                ),
                'namespaced_merged.xml'
            ),
            array('override_node.xml', 'override_node_new.xml', array(), 'override_node_merged.xml'),
            array('override_node_new.xml', 'override_node.xml', array(), 'override_node_merged.xml'),
            array('text_node.xml', 'text_node_new.xml', array(), 'text_node_merged.xml'),
        );
    }

    /**
     * @param string $xmlFile
     * @param string $newXmlFile
     * @dataProvider mergeExceptionDataProvider
     * @expectedException Magento_Exception
     */
    public function testMergeException($xmlFile, $newXmlFile)
    {
        $xml = file_get_contents(__DIR__ . "/_files/dom/{$xmlFile}");
        $newXml = file_get_contents(__DIR__ . "/_files/dom/{$newXmlFile}");
        $config = new Magento_Config_Dom($xml, array());
        $config->merge($newXml);
    }

    /**
     * @return array
     */
    public function mergeExceptionDataProvider()
    {
        return array(
            array('ambiguous_two.xml', 'ambiguous_new_one.xml')
        );
    }

    /**
     * @param string $xml
     * @param array $expectedErrors
     * @dataProvider validateDataProvider
     */
    public function testValidate($xml, array $expectedErrors)
    {
        $dom = new Magento_Config_Dom($xml);
        $actualResult = $dom->validate(__DIR__ . '/_files/sample.xsd', $actualErrors);
        $this->assertEquals(empty($expectedErrors), $actualResult);
        $this->assertEquals($expectedErrors, $actualErrors);
    }

    /**
     * @return array
     */
    public function validateDataProvider()
    {
        return array(
            'valid' => array(
                '<root><node id="id1"/><node id="id2"/></root>',
                array()
            ),
            'invalid' => array(
                '<root><node id="id1"/><unknown_node/></root>',
                array("Element 'unknown_node': This element is not expected. Expected is ( node ).\nLine: 1\n")
            ),
        );
    }

    public function testValidateCustomErrorFormat()
    {
        $xml = '<root><unknown_node/></root>';
        $errorFormat = 'Error: `%message%`';
        $expectedErrors = array("Error: `Element 'unknown_node': This element is not expected. Expected is ( node ).`");
        $dom = new Magento_Config_Dom($xml, array(), null, $errorFormat);
        $actualResult = $dom->validate(__DIR__ . '/_files/sample.xsd', $actualErrors);
        $this->assertFalse($actualResult);
        $this->assertEquals($expectedErrors, $actualErrors);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Error format '%message%,%unknown%' contains unsupported placeholders
     */
    public function testValidateCustomErrorFormatInvalid()
    {
        $xml = '<root><unknown_node/></root>';
        $errorFormat = '%message%,%unknown%';
        $dom = new Magento_Config_Dom($xml, array(), null, $errorFormat);
        $dom->validate(__DIR__ . '/_files/sample.xsd');
    }
}
