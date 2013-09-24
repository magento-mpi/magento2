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

namespace Magento\Code\Generator;

class ClassTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test class name for generation test
     */
    const TEST_CLASS_NAME = 'Magento\Code\Generator\TestAsset\TestGenerationClass';

    /**
     * Expected arguments for test class constructor
     *
     * @var array
     */
    protected $_expectedArguments = array(
        0 => 'Magento\Code\Generator\TestAsset\ParentClass',
        1 => 'Magento\Code\Generator\TestAsset\SourceClass',
        2 => 'Not_Existing_Class',
    );

    public function testGenerateForConstructor()
    {
        $generatorMock = $this->getMock('Magento\Code\Generator', array('generateClass'), array(), '', false);
        foreach ($this->_expectedArguments as $order => $class) {
            $generatorMock->expects($this->at($order))
                ->method('generateClass')
                ->with($class);
        }

        $classGenerator = new \Magento\Code\Generator\ClassGenerator($generatorMock);
        $classGenerator->generateForConstructor(self::TEST_CLASS_NAME);
    }
}
