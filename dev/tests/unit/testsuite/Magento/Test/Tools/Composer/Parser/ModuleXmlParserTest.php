<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Test\Tools\Composer\Parser;

use Magento\TestFramework\Helper\ObjectManager;

/**
 * Class ModuleXmlParserTest
 *
 * @package Magento\Test\Tools\Composer\Parser
 */
class ModuleXmlParserTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Module Xml Parser
     *
     * @var \Magento\Tools\Composer\Parser\ModuleXmlParser
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
    private $_componentDir  = '/app/code/Magento/SampleModule';

    /**
     * Intial Setup
     * @return void
     */
    protected function setUp()
    {
        $this->_rootDir = __DIR__ . '/../_files';
        $objectManagerHelper = new ObjectManager($this);
        $this->parser = $objectManagerHelper->getObject('\Magento\Tools\Composer\Parser\ModuleXmlParser');
    }

    /**
     * Test GetMappings
     * @return void
     */
    public function testgetMappings()
    {
        $moduleDefinition = $this->parser->getMappings($this->_rootDir, $this->_componentDir);
        $this->assertEquals($moduleDefinition['name'], "Magento_SampleModule");
        $this->assertEquals($moduleDefinition['version'], "1.2.3");
        $this->assertEquals(sizeof($moduleDefinition['dependencies']), 1);
    }

    /**
     * Test ChangeModule Directory
     * @return void
     */
    public function testChangeModuleDir()
    {
        $this->parser->getMappings($this->_rootDir, $this->_componentDir);
        $this->endsWith(realpath($this->parser->getFile()->getPathname()),
                        "/app/code/Magento/SampleModule/etc/module.xml");
    }

    /**
     * Helper method to check string endswith
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