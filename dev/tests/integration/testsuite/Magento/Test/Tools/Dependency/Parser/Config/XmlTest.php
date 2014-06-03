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
        $expected = array(
            array(
                'name' => 'Module1',
                'dependencies' => array(
                    array('module' => 'Magento\Core', 'type' => ''),
                    array('module' => 'Magento\Backend', 'type' => 'soft'),
                    array('module' => 'Module1', 'type' => '')
                )
            ),
            array(
                'name' => 'Module2',
                'dependencies' => array(
                    array('module' => 'Magento\Core', 'type' => ''),
                    array('module' => 'Module2', 'type' => '')
                )
            )
        );

        $actual = $this->parser->parse(
            array('files_for_parse' => array($this->fixtureDir . 'config1.xml', $this->fixtureDir . 'config2.xml'))
        );

        $this->assertEquals($expected, $actual);
    }
}
