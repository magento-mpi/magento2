<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Di
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Di_Definition_CompilerDefinition_ZendTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers Magento_Di_Definition_CompilerDefinition_Zend::addDirectory
     * @covers Magento_Di_Definition_CompilerDefinition_Zend::compile
     * @covers Magento_Di_Definition_CompilerDefinition_Zend::toArray
     */
    public function testCompiler()
    {
        $compiler = new Magento_Di_Definition_CompilerDefinition_Zend();

        $compiler->addDirectory(__DIR__ . '/TestAsset');
        $compiler->compile();

        $expectedDefinitions = include (__DIR__ . '/_files/definitions.php');
        $actualDefinitions   = $compiler->toArray();
        $this->assertEquals($expectedDefinitions, $actualDefinitions);
    }
}
