<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\ObjectManager\Config\Mapper;

class ArgumentParserTest extends \PHPUnit_Framework_TestCase
{
    public function testParse()
    {
        $document = new \DOMDocument();
        $document->load(__DIR__ . '/_files/argument_parser.xml');
        $parser = new ArgumentParser();
        $actual = $parser->parse($document->getElementsByTagName('argument')->item(0));
        $expected = [
            'item' => [
                'one' => ['name' => 'one', 'value' => 'value1'],
                'nested' => [
                    'name' => 'nested',
                    'item' => [
                        'two' => ['name' => 'two', 'value' => 'value2'],
                        'three' => ['name' => 'three', 'value' => 'value3'],
                    ],
                ],
            ],
        ];
        $this->assertSame($expected, $actual);
    }
}
