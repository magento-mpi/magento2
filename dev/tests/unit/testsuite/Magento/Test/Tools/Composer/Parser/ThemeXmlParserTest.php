<?php

namespace Magento\Test\Tools\Composer\Parser;

use Magento\TestFramework\Helper\ObjectManager;

/**
 * Class ThemeXmlParserTest
 * @package Magento\Test\Tools\Composer\Parser
 */
class ThemeXmlParserTest extends \PHPUnit_Framework_TestCase
{

    /**
     * ThemeXml Parser
     *
     * @var \Magento\Tools\Composer\Parser\ThemeXmlParser
     */
    protected $parser;

    /**
     * Initial Setup
     * @return void
     */
    protected function setUp()
    {
        $dir = __DIR__ . '/../_files/app/design/adminhtml/Magento/Sample';
        $objectManagerHelper = new ObjectManager($this);
        $this->parser = $objectManagerHelper->getObject('\Magento\Tools\Composer\Parser\ThemeXmlParser',
                                                        array('componentDir' => $dir));
    }

    /**
     * Test GetMappings
     * @return void
     */
    public function testgetMappings()
    {
        $moduleDefinition = $this->parser->getMappings();
        $this->assertEquals($moduleDefinition['name'], "Magento_Sample-Theme");
        $this->assertEquals($moduleDefinition['version'], "1.2.3.4");
        $this->assertEquals(sizeof($moduleDefinition['dependencies']), 1);
    }

    /**
     * Test Change Module Directory
     * @return void
     */
    public function testChangeModuleDir()
    {
        $this->assertTrue($this->endsWith(realpath($this->parser->getFile()->getPathname()),
                                          "/app/design/adminhtml/Magento/Sample/theme.xml"));
    }

    /**
     * Helper method to test Ends With
     *
     * @param string $haystack
     * @param string $needle
     * @return bool
     */
    public function endsWith($haystack, $needle)
    {
        return $needle === "" || substr($haystack, -strlen($needle)) === $needle;
    }
}