<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Code
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
require_once __DIR__ . DIRECTORY_SEPARATOR . 'GeneratorTest' . DIRECTORY_SEPARATOR . 'SimpleClass.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'GeneratorTest' . DIRECTORY_SEPARATOR . 'SimpleClassPluginA.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'GeneratorTest' . DIRECTORY_SEPARATOR . 'SimpleClassPluginB.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'GeneratorTest' . DIRECTORY_SEPARATOR . 'SimpleObjectManager.php';

class Magento_Code_Plugin_InvocationChainTest extends PHPUnit_Framework_TestCase
{
    public function testProceed()
    {
        $invocationChain = new \Magento\Code\Plugin\InvocationChain(
            new Magento_Code_Plugin_GeneratorTest_SimpleClass(),
            'doWork',
            new Magento_Code_Plugin_GeneratorTest_SimpleObjectManager(),
            array('Magento_Code_Plugin_GeneratorTest_SimpleClassPluginA',
                  'Magento_Code_Plugin_GeneratorTest_SimpleClassPluginB')
        );
        $this->assertEquals(
            '<PluginA><PluginB>simple class return value</PluginB></PluginA>',
            $invocationChain->proceed(array())
        );
    }
}
