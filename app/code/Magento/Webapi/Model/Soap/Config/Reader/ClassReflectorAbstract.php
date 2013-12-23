<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Webapi\Model\Soap\Config\Reader;

use Zend\Server\Reflection;
use Zend\Server\Reflection\ReflectionMethod;

/**
 * Abstract class reflector for config reader.
 */
abstract class ClassReflectorAbstract
{
    /** @var \Magento\Webapi\Model\Soap\Config\Reader\TypeProcessor */
    protected $_typeProcessor;

    /**
     * Construct reflector.
     *
     * @param \Magento\Webapi\Model\Soap\Config\Reader\TypeProcessor $typeProcessor
     */
    public function __construct(
        \Magento\Webapi\Model\Soap\Config\Reader\TypeProcessor $typeProcessor
    ) {
        $this->_typeProcessor = $typeProcessor;
    }

    /**
     * Retrieve data that has been collected during reflection of all classes.
     *
     * @return array
     */
    abstract public function getPostReflectionData();

    /**
     * Reflect methods in given class and set retrieved data into reader.
     *
     * @param $className
     * @return array
     */
    public function reflectClassMethods($className)
    {
        $data = array(
            'service' => $className,
        );
        $classReflection = new \Zend\Server\Reflection\ReflectionClass(new \ReflectionClass($className));
        /** @var $methodReflection ReflectionMethod */
        foreach ($classReflection->getMethods() as $methodReflection) {
            $data['methods'][$methodReflection->getName()] = $this->extractMethodData($methodReflection);
        }
        return array('services' => array($className => $data));
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
        $methodData = array('documentation' => $method->getDescription());
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
