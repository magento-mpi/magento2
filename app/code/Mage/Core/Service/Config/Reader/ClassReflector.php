<?php
use Zend\Server\Reflection,
    Zend\Code\Reflection\DocBlockReflection,
    Zend\Server\Reflection\ReflectionMethod;

/**
 * Class reflector for service config reader.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Service_Config_Reader_ClassReflector
{
    const METHOD_TYPE_ANNOTATION = 'Type';
    const METHOD_TYPE_CALL = 'call';

    /** @var Mage_Webapi_Helper_Config */
    protected $_helper;

    /** @var Mage_Core_Service_Config_Reader_TypeProcessor */
    protected $_typeProcessor;

    /**
     * @var Mage_Core_Model_Event_Manager
     */
    protected $_eventManager;

    /**
     * Construct reflector.
     *
     * @param Mage_Webapi_Helper_Config $helper
     * @param Mage_Core_Service_Config_Reader_TypeProcessor $typeProcessor
     * @param Mage_Core_Model_Event_Manager $eventManager
     */
    public function __construct(
        Mage_Webapi_Helper_Config $helper,
        Mage_Core_Service_Config_Reader_TypeProcessor $typeProcessor,
        Mage_Core_Model_Event_Manager $eventManager
    ) {
        $this->_helper = $helper;
        $this->_typeProcessor = $typeProcessor;
        $this->_eventManager = $eventManager;
    }

    /**
     * Retrieve data that has been collected during reflection of all classes.
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
     * @param $className
     * @return array
     */
    public function reflectClassMethods($className)
    {
        $data = array(
            'controller' => $className,
        );
        foreach ($this->_getServiceMethodsReflection($className) as $methodReflection) {
            $methodData = $this->extractMethodData($methodReflection);
            // TODO: Temporary solution
            $methodDataObject = new Varien_Object($methodData);
            $this->_eventManager->dispatch(
                'core_service_config_reader_reflect_method_data',
                array('method_data' => $methodDataObject, 'method_reflection' => $methodReflection)
            );
            $data['methods'][$methodReflection->getName()] = $methodDataObject->getData();
        }

        return array(
            'resources' => array(
                $this->_helper->translateServiceName($className) => $data,
            ),
        );
    }

    /**
     * Retrieve class methods that are exposed as services.
     *
     * @param string $className
     * @return array
     */
    protected function _getServiceMethodsReflection($className)
    {
        $serviceMethods = array();
        $serverReflection = new Reflection;
        foreach ($serverReflection->reflectClass($className)->getMethods() as $methodReflection) {
            $methodDocumentation = $methodReflection->getDocComment();
            if ($methodDocumentation) {
                /** Zend server reflection is not able to work with annotation tags of the method. */
                $docBlock = new DocBlockReflection($methodDocumentation);
                $methodType = $docBlock->getTag(self::METHOD_TYPE_ANNOTATION);
                if ($methodType && ($methodType->getContent() == self::METHOD_TYPE_CALL)) {
                    $serviceMethods[] = $methodReflection;
                }
            }
        }
        return $serviceMethods;
    }

    /**
     * Retrieve method interface and documentation description.
     *
     * @param ReflectionMethod $method
     * @return array
     * @throws InvalidArgumentException
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
        $deprecationPolicy = $this->_extractDeprecationPolicy($method);
        if ($deprecationPolicy) {
            $methodData['deprecation_policy'] = $deprecationPolicy;
        }

        return $methodData;
    }

    /**
     * Extract method deprecation policy.
     *
     * Return result in the following format:<pre>
     * array(
     *     'removed'      => true,            // either 'deprecated' or 'removed' item must be specified
     *     'deprecated'   => true,
     *     'use_resource' => 'operationName'  // resource to be used instead
     *     'use_method'   => 'operationName'  // method to be used instead
     * )
     * </pre>
     *
     * @param ReflectionMethod $methodReflection
     * @return array|bool On success array with policy details; false otherwise.
     * @throws LogicException If deprecation tag format is incorrect.
     */
    protected function _extractDeprecationPolicy(ReflectionMethod $methodReflection)
    {
        $deprecationPolicy = false;
        $methodDocumentation = $methodReflection->getDocComment();
        if ($methodDocumentation) {
            /** Zend server reflection is not able to work with annotation tags of the method. */
            $docBlock = new DocBlockReflection($methodDocumentation);
            $removedTag = $docBlock->getTag('apiRemoved');
            $deprecatedTag = $docBlock->getTag('apiDeprecated');
            if ($removedTag) {
                $deprecationPolicy = array('removed' => true);
                $useMethod = $removedTag->getContent();
            } elseif ($deprecatedTag) {
                $deprecationPolicy = array('deprecated' => true);
                $useMethod = $deprecatedTag->getContent();
            }

            if (isset($useMethod) && is_string($useMethod) && !empty($useMethod)) {
                $this->_extractDeprecationPolicyUseMethod($methodReflection, $useMethod, $deprecationPolicy);
            }
        }
        return $deprecationPolicy;
    }

    /**
     * Extract method deprecation policy "use method" data.
     *
     * @param ReflectionMethod $methodReflection
     * @param string $useMethod
     * @param array $deprecationPolicy
     * @throws LogicException
     */
    protected function _extractDeprecationPolicyUseMethod(
        ReflectionMethod $methodReflection,
        $useMethod,
        &$deprecationPolicy
    ) {
        $invalidFormatMessage = sprintf(
            'The "%s" method has invalid format of Deprecation policy. '
                . 'Accepted formats are createV1, catalogProduct::createV1 '
                . 'and Mage_Catalog_Webapi_ProductController::createV1.',
            $methodReflection->getDeclaringClass()->getName() . '::' . $methodReflection->getName()
        );
        /** Add information about what method should be used instead of deprecated/removed one. */
        /**
         * Description is expected in one of the following formats:
         * - Mage_Catalog_Webapi_ProductController::createV1
         * - catalogProduct::createV1
         * - createV1
         */
        $useMethodParts = explode('::', $useMethod);
        switch (count($useMethodParts)) {
            case 2:
                try {
                    /** Support of: Mage_Catalog_Webapi_ProductController::createV1 */
                    $serviceName = $this->_helper->translateServiceName($useMethodParts[0]);
                } catch (InvalidArgumentException $e) {
                    /** Support of: catalogProduct::createV1 */
                    $serviceName = $useMethodParts[0];
                }
                $deprecationPolicy['use_resource'] = $serviceName;
                $methodName = $useMethodParts[1];
                break;
            case 1:
                $methodName = $useMethodParts[0];
                /** If resource was not specified, current one should be used. */
                $deprecationPolicy['use_resource'] = $this->_helper->translateServiceName(
                    $methodReflection->getDeclaringClass()->getName()
                );
                break;
            default:
                throw new LogicException($invalidFormatMessage);
                break;
        }
        $deprecationPolicy['use_method'] = $methodName;
    }
}
