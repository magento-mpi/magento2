<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */
namespace Magento\Tools\Di\Compiler;

class ConstructorArgumentTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        $argument = ['configuration', 'array', true, null];
        $model = new \Magento\Tools\Di\Compiler\ConstructorArgument($argument);
        $this->assertEquals($argument[0], $model->getName());
        $this->assertEquals($argument[1], $model->getType());
        $this->assertTrue($model->isRequired());
        $this->assertNull($model->getDefaultValue());
    }
}
