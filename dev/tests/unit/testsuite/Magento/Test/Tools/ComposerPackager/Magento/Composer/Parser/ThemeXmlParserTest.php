<?php

namespace Magento\Test\Tools\ComposerPackager\Magento\Composer\Parser;

use Magento\TestFramework\Helper\ObjectManager;

class ThemeXmlParserTest extends \PHPUnit_Framework_TestCase
{

    protected $parser;

    protected function setUp()
    {
        $dir = __DIR__ . '/../../../_files/app/design/adminhtml/Magento/Sample';
        $objectManagerHelper = new ObjectManager($this);
        $this->parser = $objectManagerHelper->getObject('\Magento\Composer\Parser\ThemeXmlParser', array('componentDir' => $dir));
    }

    public function testgetMappings()
    {
        $moduleDefinition = $this->parser->getMappings();
        $this->assertEquals($moduleDefinition->name, "Magento_Sample-Theme");
        $this->assertEquals($moduleDefinition->version, "1.2.3.4");
        $this->assertEquals(sizeof($moduleDefinition->dependencies), 1);
    }

    public function testChangeModuleDir()
    {
        $this->assertTrue($this->endsWith(realpath($this->parser->getFile()->getPathname()), "/app/design/adminhtml/Magento/Sample/theme.xml"));
    }

    public function endsWith($haystack, $needle)
    {
        return $needle === "" || substr($haystack, -strlen($needle)) === $needle;
    }
}