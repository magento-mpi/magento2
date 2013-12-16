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
namespace Magento\Code\Plugin;

require_once __DIR__ . '/GeneratorTest/SimpleClass.php';
require_once __DIR__ . '/GeneratorTest/SimpleClassPluginA.php';
require_once __DIR__ . '/GeneratorTest/SimpleClassPluginB.php';
require_once __DIR__ . '/GeneratorTest/SimpleObjectManager.php';

class InvocationChainTest extends \PHPUnit_Framework_TestCase
{
    public function testProceed()
    {
        $invocationChain = new \Magento\Code\Plugin\InvocationChain(
            new \Magento\Code\Plugin\GeneratorTest\SimpleClass(),
            'doWork',
            new \Magento\Code\Plugin\GeneratorTest\SimpleObjectManager(),
            array('Magento\Code\Plugin\GeneratorTest\SimpleClassPluginA',
                  'Magento\Code\Plugin\GeneratorTest\SimpleClassPluginB')
        );
        $this->assertEquals(
            '<PluginA><PluginB>simple class return value</PluginB></PluginA>',
            $invocationChain->proceed(array())
        );
    }
}
