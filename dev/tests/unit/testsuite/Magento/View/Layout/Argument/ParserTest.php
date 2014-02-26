<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Layout\Argument;

class ParserTest extends \PHPUnit_Framework_TestCase
{
    public function testParse()
    {
        $document = new \DOMDocument();
        $document->load(__DIR__ . '/_files/arguments.xml');
        $parser = new \Magento\View\Layout\Argument\Parser;
        $actual = $parser->parse($document->getElementsByTagName('argument')->item(0));
        $expected = array(
            'updater' => array(
                'Updater1',
                'Updater2',
            ),
            'param' => array(
                'param1' => array(
                    'name' => 'param1',
                    'value' => 'Param Value 1',
                ),
                'param2' => array(
                    'name' => 'param2',
                    'value' => 'Param Value 2',
                ),
            ),
            'item' => array(
                'item1' => array(
                    'name' => 'item1',
                    'value' => 'Item Value 1',
                ),
                'item2' => array(
                    'name' => 'item2',
                    'item' => array(
                        'item3' => array(
                            'name' => 'item3',
                            'value' => 'Item Value 2.3',
                        ),
                    ),
                ),
            ),
        );
        $this->assertSame($expected, $actual);
    }
}
