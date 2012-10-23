<?php
/**
* {license_notice}
*
* @category   Magento
* @package    tools
* @copyright  {copyright}
* @license    {license_link}
*/

/**
 * Tools_Migration_System_Configuration_Parser test case
 */
require_once realpath(dirname(__FILE__) . '/../../../../../../../') . '/tools/migration/System/Configuration/Parser.php';

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

        $expected = array(
            'config' => array()
        );
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
        <some_section translate="label" module="Mage_Some">
            <label>Section Name</label>
            <tab>test</tab>
            <frontend_type>text</frontend_type>
            <sort_order>140</sort_order>
            <show_in_default>1</show_in_default>
            <show_in_website>1</show_in_website>
            <show_in_store>1</show_in_store>
            <resource>Mage_Some::resource</resource>
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
            'config' => array(
                'sections' => array(
                    'some_section' => array(
                        'label' => 'Section Name',
                        'tab' => 'test',
                        'frontend_type' => 'text',
                        'sort_order' => '140',
                        'show_in_default' => '1',
                        'show_in_website' => '1',
                        'show_in_store' => '1',
                        'resource' => 'Mage_Some::resource',
                        '@attributes' => array(
                            'translate' => 'label',
                            'module' => 'Mage_Some'
                        ),
                    )
                )
            )
        );
        $dom = new DOMDocument();
        $dom->loadXML($xml);
        $this->assertEquals($expected, $this->_parser->parse($dom));
    }

}