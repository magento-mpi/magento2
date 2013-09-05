<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    tools
 * @copyright  {copyright}
 * @license    {license_link}
 */

require_once realpath(__DIR__ . '/../../../../../../../')
    . '/tools/migration/System/Configuration/Parser.php';

/**
 * Tools_Migration_System_Configuration_Parser test case
 */
class Tools_Migration_System_Configuration_ParserTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Tools_Migration_System_Configuration_Parser
     */
    protected $_parser;

    protected function setUp()
    {
        $this->_parser = new Tools_Migration_System_Configuration_Parser();
    }

    protected function tearDown()
    {
        $this->_parser = null;
    }


    public function testParseEmptyDom()
    {
        $this->assertEquals(array(), $this->_parser->parse(new DOMDocument()));
    }

    public function testParseDomWithoutNodes()
    {
        $xml = <<<XML
<?xml version="1.0"?>
<config>
</config>
XML;

        $expected = array();
        $dom = new DOMDocument();
        $dom->loadXML($xml);
        $this->assertEquals($expected, $this->_parser->parse($dom));
    }

    public function testParseDomNodes()
    {
        $xml = <<<XML
<?xml version="1.0"?>
<!--
/**
 * some comment
 */
-->
<config>
    <sections>
        <some_section translate="label">
            <label>Section Name</label>
            <tab>test</tab>
            <frontend_type>text</frontend_type>
            <sort_order>140</sort_order>
            <show_in_default>1</show_in_default>
            <show_in_website>1</show_in_website>
            <show_in_store>1</show_in_store>
            <resource>Magento_Some::resource</resource>
        </some_section>
    </sections>
</config>
XML;

        $comment = <<<XMLCOMMENT

/**
 * some comment
 */

XMLCOMMENT;
        $expected = array(
            'comment' => $comment,
            'sections' => array(
                'some_section' => array(
                    'label' => array('#text' => 'Section Name'),
                    'tab' => array('#text' => 'test'),
                    'frontend_type' => array('#text' => 'text'),
                    'sort_order' => array('#text' => '140'),
                    'show_in_default' => array('#text' => '1'),
                    'show_in_website' => array('#text' => '1'),
                    'show_in_store' => array('#text' => '1'),
                    'resource' => array('#text' => 'Magento_Some::resource'),
                    '@attributes' => array('translate' => 'label')
                )
            )
        );
        $dom = new DOMDocument();
        $dom->loadXML($xml);
        $this->assertEquals($expected, $this->_parser->parse($dom));
    }
}
