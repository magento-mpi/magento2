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
     * @param bool $isExpectedValid
     * @dataProvider validateDataProvider
     */
    public function testValidate($xml, $isExpectedValid)
    {
        $config = new Magento_Config_Dom($xml);
        $schema = __DIR__ . '/_files/sample.xsd';
        if ($isExpectedValid) {
            $this->assertTrue($config->validate($schema));
        } else {
            $errors = array();
            $this->assertFalse($config->validate($schema, $errors));
            $this->assertNotEmpty($errors);
        }
    }

    /**
     * @return array
     */
    public function validateDataProvider()
    {
        $validXml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<root>
    <node id="id1"/>
    <node id="id2"/>
</root>
XML;
        $invalidXml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<root>
    <node id="id1"/>
    <unknown_node/>
</root>
XML;
        return array(
            array($validXml, true),
            array($invalidXml, false),
        );
    }
}
