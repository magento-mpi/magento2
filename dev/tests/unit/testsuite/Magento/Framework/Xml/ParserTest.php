<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\Xml;

class ParserTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Framework\Xml\Parser */
    protected $parser;

    protected function setUp()
    {
        $this->parser = new \Magento\Framework\Xml\Parser();
    }

    public function testGetXml()
    {
        $this->assertEquals(
            ['data' => [
                'nodes' => [
                    'text' => ' some text ',
                    'cdata' => 'Some data here',
                    'zero' => '0',
                    'null' => null,
                ]
            ]],
            $this->parser->load(__DIR__ . '/_files/data.xml')->xmlToArray()
        );
    }
}
