<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Webapi\Model\Soap\Config\Reader\Soap;

use Zend\Server\Reflection;
use Zend\Server\Reflection\ReflectionMethod;

/**
 * SOAP API specific class reflector.
 */
class ClassReflector
{
    /** @var \Magento\Webapi\Model\Soap\Config\Reader\TypeProcessor */
    protected $_typeProcessor;

    /** @var \Magento\Webapi\Helper\Config */
    protected $_configHelper;

    /**
     * Construct reflector.
     *
     * @param \Magento\Webapi\Model\Soap\Config\Reader\TypeProcessor $typeProcessor
     * @param \Magento\Webapi\Helper\Config $configHelper
     */
    public function __construct(
        \Magento\Webapi\Model\Soap\Config\Reader\TypeProcessor $typeProcessor,
        \Magento\Webapi\Helper\Config $configHelper
    ) {
        $this->_typeProcessor = $typeProcessor;
        $this->_configHelper = $configHelper;
    }

    /**
     * Set types data into reader after reflecting all files.
     *
     * @return array
     */
    public function getPostReflectionData()
    {
        return array(
            'types' => $this->_typeProcessor->getTypesData(),
            'type_to_class_map' => $this->_typeProcessor->getTypeToClassMap(),
        );
    }

    /**
     * Reflect methods in given class and set retrieved data into reader.
     *
     * @param array $methodsDeclaredInSoap
     * @param string $className
     * @return array
     */
    public function reflectClassMethods($className, $methodsDeclaredInSoap)
    {
        $data = array(
            'service' => $className,
        );
        $classReflection = new \Zend\Server\Reflection\ReflectionClass(new \ReflectionClass($className));
        /** @var $methodReflection ReflectionMethod */
        foreach ($classReflection->getMethods() as $methodReflection) {
            $methodName = $methodReflection->getName();
            if (array_key_exists($methodName, $methodsDeclaredInSoap)) {
                $data['methods'][$methodName] = $this->extractMethodData($methodReflection);
            }
        }
        // TODO: Consider moving getServiceName() to helper
        return array('services' => array($this->_configHelper->getServiceName($className) => $data));
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
