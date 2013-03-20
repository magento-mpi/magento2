<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Di
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Di_Generator_ClassTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test class name for generation test
     */
    const TEST_CLASS_NAME = 'Magento_Di_Generator_TestAsset_TestGenerationClass';

    /**
     * Expected arguments for test class constructor
     *
     * @var array
     */
    protected $_expectedArguments = array(
        0 => 'Magento\Di\Generator\TestAsset\ParentClass',
        1 => 'Magento\Di\Generator\TestAsset\SourceClass',
        2 => 'Not_Existing_Class',
    );

    public function testGenerateForConstructor()
    {
        $generatorMock = $this->getMock('Magento_Di_Generator', array('generateClass'), array(), '', false);
        foreach ($this->_expectedArguments as $order => $class) {
            $generatorMock->expects($this->at($order))
                ->method('generateClass')
                ->with($class);
        }

        $classGenerator = new Magento_Di_Generator_Class($generatorMock);
        $classGenerator->generateForConstructor(self::TEST_CLASS_NAME);
    }
}
