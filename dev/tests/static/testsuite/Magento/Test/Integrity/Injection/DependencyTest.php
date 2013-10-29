<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Test\Integrity\Injection;

use Magento\TestFramework\Utility\Files;
use Zend\Code\Reflection\ClassReflection;
use Zend\Code\Reflection\FileReflection;
use Zend\Code\Reflection\ParameterReflection;

/**
 * @package Magento\Test\Integrity\Injection
 */
class DependencyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var array
     */
    protected $errors = array();

    /**
     * Fix for one of our class in library implemented interface from application
     *
     * @TODO: remove this code when class Magento\Data\Collection will fixed
     */
    public static function setUpBeforeClass()
    {
        include_once __DIR__ . '/../../../../../../../../app/code/Magento/Core/Model/Option/ArrayInterface.php';
    }

    /**
     * Test check injectable dependencies in library
     *
     * @test
     * @dataProvider libraryDataProvider
     */
    public function testCheckDependencies($file)
    {
        $this->markTestSkipped('Skipped while all application dependencies will be removed from library');
        include_once $file;
        $fileReflection = new FileReflection($file);

        foreach ($fileReflection->getClasses() as $class) {
            /** @var ClassReflection $class */
            foreach ($class->getMethods() as $method) {
                /** @var \Zend\Code\Reflection\MethodReflection $method */
                foreach ($method->getParameters() as $parameter) {
                    /** @var ParameterReflection $parameter */
                    $this->detectWrongDependencies($parameter, $class);
                }
            }
        }

        if ($this->hasErrors()) {
            $this->fail($this->getFailMessage());
        }
    }

    /**
     * Detect wrong dependencies
     *
     * @param ParameterReflection $parameter
     * @param ClassReflection $class
     * @throws \Exception
     * @throws \ReflectionException
     */
    public function detectWrongDependencies(ParameterReflection $parameter, ClassReflection $class)
    {
        try {
            $parameter->getClass();
        } catch (\ReflectionException $e) {
            if (preg_match('#^Class ([A-Za-z\\\\]+) does not exist$#', $e->getMessage(), $result)) {
                $this->errors[$class->getName()][] = $result[1];
            } else {
                throw $e;
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function tearDown()
    {
        $this->errors = array();
    }

    /**
     * Check if file has wrong dependencies
     *
     * @return bool
     */
    protected function hasErrors()
    {
        return empty($this->errors);
    }

    /**
     * Prepare failed message
     *
     * @return string
     */
    protected function getFailMessage()
    {
        $failMessage = '';
        foreach ($this->errors as $class => $dependencies) {
            $failMessage .= $class . ' depends for non-library '
                . (count($dependencies) > 1 ? 'classes ' : 'class ');
            foreach ($dependencies as $dependency) {
                $failMessage .= $dependency . ' ';
            }
            $failMessage = trim($failMessage) . PHP_EOL;
        }
        return $failMessage;
    }

    /**
     * Contains all library files
     *
     * @return array
     */
    public function libraryDataProvider()
    {
        return Files::init()->getClassFiles(false, false, false, false, false, true, true);
    }
}
