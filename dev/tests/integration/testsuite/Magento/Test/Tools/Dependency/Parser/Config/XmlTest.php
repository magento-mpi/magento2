<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Test\Tools\Dependency\Parser\Config;

use Magento\Tools\Dependency\Parser\Config\Xml;

class XmlTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected $fixtureDir;

    /**
     * @var \Magento\Tools\Dependency\Parser\Config
     */
    protected $parser;

    protected function setUp()
    {
        $this->fixtureDir = realpath(__DIR__ . '/../../_files') . '/';

        $this->parser = new Xml();
    }

    public function testParse()
    {
        $expected = [
            ['name' => 'Module1', 'dependencies' => [
                ['module' => 'Magento\Core', 'type' => ''],
                ['module' => 'Magento\Backend', 'type' => 'soft'],
                ['module' => 'Module1', 'type' => ''],
            ]],
            ['name' => 'Module2', 'dependencies' => [
                ['module' => 'Magento\Core', 'type' => ''],
                ['module' => 'Module2', 'type' => ''],
            ]],
        ];

        $actual = $this->parser->parse([
            'files_for_parse' => [
                $this->fixtureDir . 'config1.xml',
                $this->fixtureDir . 'config2.xml',
            ],
        ]);

        $this->assertEquals($expected, $actual);
    }
}
