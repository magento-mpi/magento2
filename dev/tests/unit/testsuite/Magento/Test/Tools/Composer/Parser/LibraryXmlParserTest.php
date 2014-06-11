<?php

namespace Magento\Test\Tools\Composer\Parser;

use Magento\TestFramework\Helper\ObjectManager;

/**
 * Class LibraryXmlParserTest
 * @package Magento\Test\Tools\Composer\Parser
 */
class LibraryXmlParserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Library Xml Parser
     *
     * @var \Magento\Tools\Composer\Parser\LibraryXmlParser
     */
    protected $parser;

    /**
     * Magneto Root Directory
     *
     * @var string
     */
    private $_rootDir;

    /**
     * Component Root Directory
     *
     * @var string
     */
    private $_componentDir;

    /**
     * Initial Setup
     * @return void
     */
    protected function setUp()
    {
        $this->_rootDir = __DIR__ . '/../_files';
        $this->_componentDir =  '/lib';
        $objectManagerHelper = new ObjectManager($this);
        $this->parser = $objectManagerHelper->getObject('\Magento\Tools\Composer\Parser\LibraryXmlParser');
    }

    /**
     * Test Get Mappings
     * @return void
     */
    public function testGetMappings()
    {
        $libraryDefinition = $this->parser->getMappings($this->_rootDir, $this->_componentDir );
        $this->assertEquals($libraryDefinition['name'], "Magento_Library");
        $this->assertEquals($libraryDefinition['version'], "2.1.0");
        $this->assertEquals(sizeof($libraryDefinition['dependencies']), 1);
    }

    /**
     * Test Change Module Dir
     * @return void
     */
    public function testChangeModuleDir()
    {
        $this->parser->getMappings($this->_rootDir, $this->_componentDir );
        $this->endsWith(realpath($this->parser->getFile()->getPathname()), "lib/library.xml");
    }

    /**
     * Helper method for endswith
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