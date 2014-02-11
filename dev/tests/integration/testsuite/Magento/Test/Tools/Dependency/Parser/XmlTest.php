<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Test\Tools\Dependency\Parser;

use Magento\Tools\Dependency\Config;
use Magento\Tools\Dependency\Dependency;
use Magento\Tools\Dependency\Module;
use Magento\Tools\Dependency\Parser;

class XmlTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected $fixtureDir;

    /**
     * @var \Magento\Tools\Dependency\Parser\Xml
     */
    protected $parser;

    protected function setUp()
    {
        $this->fixtureDir = realpath(__DIR__ . '/../_files') . '/';

        $this->parser = new Parser\Xml();
    }

    public function testParse()
    {
        $expected = new Config([
            new Module('Module1', [
                new Dependency('Magento_Core'),
                new Dependency('Magento_Backend', Dependency::TYPE_SOFT),
                new Dependency('Module1'),
            ]),
            new Module('Module2', [
                new Dependency('Magento_Core'),
                new Dependency('Module2'),
            ]),
        ]);

        $actual = $this->parser->parse([
            $this->fixtureDir . 'config1.xml',
            $this->fixtureDir . 'config2.xml',
        ]);

        $this->assertEquals($expected, $actual);
    }
}
