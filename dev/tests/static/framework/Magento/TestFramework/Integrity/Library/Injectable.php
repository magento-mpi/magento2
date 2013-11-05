<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TestFramework\Integrity\Library;

use Zend\Code\Reflection\ClassReflection;
use Zend\Code\Reflection\FileReflection;
use Zend\Code\Reflection\ParameterReflection;

/**
 * @package Magento\TestFramework
 */
class Injectable
{
    /**
     * @var \ReflectionException[]
     */
    protected $exceptions = array();

    /**
     * @param FileReflection $fileReflection
     */
    public function checkDependencies(FileReflection $fileReflection)
    {
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
    }

    /**
     * Detect wrong dependencies
     *
     * @param ParameterReflection $parameter
     * @throws \Exception
     * @throws \ReflectionException
     */
    protected function detectWrongDependencies(ParameterReflection $parameter)
    {
        try {
            $parameter->getClass();
        } catch (\ReflectionException $e) {
            $this->exceptions[] = $e;
        }
    }

    /**
     * @return \ReflectionException[]
     */
    public function getDependencies()
    {
        return $this->exceptions;
    }
}
