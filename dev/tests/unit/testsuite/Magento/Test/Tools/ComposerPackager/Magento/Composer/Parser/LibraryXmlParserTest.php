<?php

namespace Magento\Test\Tools\ComposerPackager\Magento\Composer\Parser;

use Magento\TestFramework\Helper\ObjectManager;

class LibraryXmlParserTest extends \PHPUnit_Framework_TestCase
{
    protected $parser;

    protected function setUp()
    {
        $libraryDir = __DIR__ . '/../../../_files/lib';
        $objectManagerHelper = new ObjectManager($this);
        $this->parser = $objectManagerHelper->getObject('\Magento\Composer\Parser\LibraryXmlParser', array('libraryDir' => $libraryDir));
    }

    public function testGetMappings()
    {
        $libraryDefinition = $this->parser->getMappings();
        $this->assertEquals($libraryDefinition->name, "Magento_Library");
        $this->assertEquals($libraryDefinition->version, "2.1.0");
        $this->assertEquals(sizeof($libraryDefinition->dependencies), 1);
    }

    public function testChangeModuleDir()
    {
        $this->endsWith(realpath($this->parser->getFile()->getPathname()), "/app/code/Magento/lib/library.xml");
    }

    public function endsWith($haystack, $needle)
    {
        return $needle === "" || substr($haystack, -strlen($needle)) === $needle;
    }
}