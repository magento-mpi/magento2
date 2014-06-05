<?php

namespace Magento\Test\Tools\Composer\Parser;

use Magento\TestFramework\Helper\ObjectManager;

class ModuleXmlParserTest extends \PHPUnit_Framework_TestCase
{

    protected $parser;

    protected function setUp()
    {
        $moduleDir = __DIR__ . '/../_files/app/code/Magento/SampleModule';
        $objectManagerHelper = new ObjectManager($this);
        $this->parser = $objectManagerHelper->getObject('\Magento\Tools\Composer\Parser\ModuleXmlParser', array('componentDir' => $moduleDir));
    }

    public function testgetMappings()
    {
        $moduleDefinition = $this->parser->getMappings();
        $this->assertEquals($moduleDefinition->name, "Magento_SampleModule");
        $this->assertEquals($moduleDefinition->version, "1.2.3");
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