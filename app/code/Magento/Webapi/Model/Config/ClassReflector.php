<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Webapi\Model\Config;

use Zend\Server\Reflection;
use Zend\Server\Reflection\ReflectionMethod;

/**
 * Class reflector.
 */
class ClassReflector
{
    /** @var \Magento\Webapi\Model\Config\ClassReflector\TypeProcessor */
    protected $_typeProcessor;

    /**
     * Construct reflector.
     *
     * @param \Magento\Webapi\Model\Config\ClassReflector\TypeProcessor $typeProcessor
     */
    public function __construct(\Magento\Webapi\Model\Config\ClassReflector\TypeProcessor $typeProcessor)
    {
        $this->_typeProcessor = $typeProcessor;
    }

    /**
     * Reflect methods in given class and set retrieved data into reader.
     *
     * @param string $className
     * @param array $methods
     * @return array <pre>array(
     *     $firstMethod => array(
     *         'documentation' => $methodDocumentation,
     *         'interface' => array(
     *             'in' => array(
     *                 'parameters' => array(
     *                     $firstParameter => array(
     *                         'type' => $type,
     *                         'required' => $isRequired,
     *                         'documentation' => $parameterDocumentation
     *                     ),
     *                     ...
     *                 )
     *             ),
     *             'out' => array(
     *                 'parameters' => array(
     *                     $firstParameter => array(
     *                         'type' => $type,
     *                         'required' => $isRequired,
     *                         'documentation' => $parameterDocumentation
     *                     ),
     *                     ...
     *                 )
     *             )
     *         )
     *     ),
     *     ...
     * )</pre>
     */
    public function reflectClassMethods($className, $methods)
    {
        $data = array();
        $classReflection = new \Zend\Server\Reflection\ReflectionClass(new \ReflectionClass($className));
        /** @var $methodReflection ReflectionMethod */
        foreach ($classReflection->getMethods() as $methodReflection) {
            $methodName = $methodReflection->getName();
            if (array_key_exists($methodName, $methods)) {
                $data[$methodName] = $this->extractMethodData($methodReflection);
            }
        }
        return $data;
    }

    /**
     * Retrieve method interface and documentation description.
     *
     * @param ReflectionMethod $method
     * @return array
     * @throws \InvalidArgumentException
     */
    public function extractMethodData(ReflectionMethod $method)
    {
        $methodData = array('documentation' => $method->getDescription(), 'interface' => array());
        $prototypes = $method->getPrototypes();
        /** Take the fullest interface that also includes optional parameters. */
        /** @var \Zend\Server\Reflection\Prototype $prototype */
        $prototype = end($prototypes);
        /** @var \Zend\Server\Reflection\ReflectionParameter $parameter */
        foreach ($prototype->getParameters() as $parameter) {
            $parameterData = array(
                'type' => $this->_typeProcessor->process($parameter->getType()),
                'required' => !$parameter->isOptional(),
                'documentation' => $parameter->getDescription(),
            );
            if ($parameter->isOptional()) {
                $parameterData['default'] = $parameter->getDefaultValue();
            }
            $methodData['interface']['in']['parameters'][$parameter->getName()] = $parameterData;
        }
        if ($prototype->getReturnType() != 'void') {
            $methodData['interface']['out']['parameters']['result'] = array(
                'type' => $this->_typeProcessor->process($prototype->getReturnType()),
                'documentation' => $prototype->getReturnValue()->getDescription(),
                'required' => true,
            );
        }

        return $methodData;
    }
}
