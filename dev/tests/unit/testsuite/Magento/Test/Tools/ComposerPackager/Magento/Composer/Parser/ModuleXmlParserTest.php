<?php

namespace Magento\Test\Tools\ComposerPackager\Magento\Composer\Parser;

use Magento\TestFramework\Helper\ObjectManager;

class ModuleXmlParserTest extends \PHPUnit_Framework_TestCase
{

    protected $parser;

    protected function setUp()
    {
        $moduleDir = __DIR__ . '/../../../_files/app/code/Magento/SampleModule';
        $objectManagerHelper = new ObjectManager($this);
        $this->parser = $objectManagerHelper->getObject('\Magento\Composer\Parser\ModuleXmlParser', array('moduleDir' => $moduleDir));
    }

    public function testgetMappings()
    {
        $moduleDefinition = $this->parser->getMappings();
        $this->assertEquals($moduleDefinition->name, "Magento_SampleModule");
        $this->assertEquals($moduleDefinition->version, "1.2.3");
        $this->assertTrue($moduleDefinition->active);
        $this->assertEquals(sizeof($moduleDefinition->dependencies), 1);
    }

    public function testChangeModuleDir()
    {
        $this->endsWith(realpath($this->parser->getFile()->getPathname()), "/app/code/Magento/SampleModule/etc/module.xml");
    }

    public function endsWith($haystack, $needle)
    {
        return $needle === "" || substr($haystack, -strlen($needle)) === $needle;
    }
}