<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ObjectManager\Config\Mapper;

class ArgumentParserTest extends \PHPUnit_Framework_TestCase
{
    public function testParse()
    {
        $document = new \DOMDocument();
        $document->load(__DIR__ . '/_files/argument_parser.xml');
        $parser = new ArgumentParser;
        $actual = $parser->parse($document->getElementsByTagName('argument')->item(0));
        $expected = array(
            'item' => array(
                'one' => array(
                    'name' => 'one',
                    'value' => 'value1',
                ),
                'nested' => array(
                    'name' => 'nested',
                    'item' => array(
                        'two' => array(
                            'name' => 'two',
                            'value' => 'value2',
                        ),
                        'three' => array(
                            'name' => 'three',
                            'value' => 'value3',
                        ),
                    ),
                ),
            ),
        );
        $this->assertSame($expected, $actual);
    }
} 
