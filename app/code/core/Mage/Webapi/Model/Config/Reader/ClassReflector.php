<?php
use Zend\Server\Reflection,
    Zend\Code\Reflection\DocBlockReflection,
    Zend\Server\Reflection\ReflectionMethod;

/**
 * Class reflector for config reader.
 *
 * @copyright {}
 */
class Mage_Webapi_Model_Config_Reader_ClassReflector
{
    /**
     * @var Mage_Webapi_Helper_Data
     */
    protected $_helper;

    /**
     * @var Mage_Webapi_Model_Config_Reader_TypeProcessor
     */
    protected $_typeProcessor;

    /**
     * @var Mage_Webapi_Model_Config_Reader_RouteGenerator
     */
    protected $_routeGenerator;

    /**
     * @var array
     */
    protected $_restRoutes = array();

    public function __construct(
        Mage_Webapi_Helper_Data $helper,
        Mage_Webapi_Model_Config_Reader_TypeProcessor $typeProcessor,
        Mage_Webapi_Model_Config_Reader_RouteGenerator $routeGenerator
    ) {
        $this->_helper = $helper;
        $this->_typeProcessor = $typeProcessor;
        $this->_routeGenerator = $routeGenerator;
    }

    public function reflectClassMethods($className)
    {
        $data = array();
        $serverReflection = new Reflection;
        foreach ($serverReflection->reflectClass($className)->getMethods() as $methodReflection) {
            try {
                $method = $this->getMethodNameWithoutVersionSuffix($methodReflection);
            } catch (InvalidArgumentException $e) {
                /** Resources can contain methods that should not be exposed through API. */
                continue;
            }
            $version = $this->getMethodVersion($methodReflection);
            if ($version) {
                $methodMetaData = $this->_extractMethodData($methodReflection);
                $data['versions'][$version]['methods'][$method] = $methodMetaData;
                $restRoutes = $this->_routeGenerator->generateRestRoutes($methodReflection);
                $data['versions'][$version]['methods'][$method]['rest_routes'] = array_keys($restRoutes);
                $this->_restRoutes = array_merge($this->_restRoutes, $restRoutes);
            }
        }
        // Sort versions array for further fallback.
        ksort($data['versions']);

        return $data;
    }

    public function getRestRoutes()
    {
        return $this->_restRoutes;
    }

    /**
     * Get type processor object.
     *
     * @return Mage_Webapi_Model_Config_Reader_TypeProcessor
     */
    public function getTypeProcessor()
    {
        return $this->_typeProcessor;
    }

    /**
     * Identify API method name without version suffix by its reflection.
     *
     * @param ReflectionMethod|string $method Method name or method reflection.
     * @return string Method name without version suffix on success.
     * @throws InvalidArgumentException When method name is invalid API resource method.
     */
    public function getMethodNameWithoutVersionSuffix($method)
    {
        if ($method instanceof ReflectionMethod) {
            $methodNameWithSuffix = $method->getName();
        } else {
            $methodNameWithSuffix = $method;
        }
        $regularExpression = $this->_getMethodNameRegularExpression();
        if (preg_match($regularExpression, $methodNameWithSuffix, $methodMatches)) {
            $methodName = $methodMatches[1];
            return $methodName;
        }
        throw new InvalidArgumentException(sprintf('"%s" is an invalid API resource method.', $methodNameWithSuffix));
    }

    /**
     * Identify API method version by its reflection.
     *
     * @param ReflectionMethod $methodReflection
     * @return string|bool Method version with prefix on success.
     *      false is returned in case when method should not be exposed via API.
     */
    public function getMethodVersion(ReflectionMethod $methodReflection)
    {
        $methodVersion = false;
        $methodNameWithSuffix = $methodReflection->getName();
        $regularExpression = $this->_getMethodNameRegularExpression();
        if (preg_match($regularExpression, $methodNameWithSuffix, $methodMatches)) {
            $resourceNamePosition = 2;
            $methodVersion = ucfirst($methodMatches[$resourceNamePosition]);
        }
        return $methodVersion;
    }

    /**
     * Get regular expression to be used for method name separation into name itself and version.
     *
     * @return string
     */
    protected function _getMethodNameRegularExpression()
    {
        return sprintf('/(%s)(V\d+)/', implode('|', $this->_helper->getAllowedMethods()));
    }

    /**
     * Retrieve method interface and documentation description.
     *
     * @param ReflectionMethod $method
     * @return array
     * @throws InvalidArgumentException
     */
    protected function _extractMethodData(ReflectionMethod $method)
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
     *     'use_version'  => N,               // version of method to be used instead
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
                    $resourceName = $this->_helper->translateResourceName($useMethodParts[0]);
                } catch (InvalidArgumentException $e) {
                    /** Support of: catalogProduct::createV1 */
                    $resourceName = $useMethodParts[0];
                }
                $deprecationPolicy['use_resource'] = $resourceName;
                $methodName = $useMethodParts[1];
                break;
            case 1:
                $methodName = $useMethodParts[0];
                /** If resource was not specified, current one should be used. */
                $deprecationPolicy['use_resource'] = $this->_helper->translateResourceName(
                    $methodReflection->getDeclaringClass()->getName()
                );
                break;
            default:
                throw new LogicException($invalidFormatMessage);
                break;
        }
        try {
            $methodWithoutVersion = $this->getMethodNameWithoutVersionSuffix($methodName);
        } catch (Exception $e) {
            throw new LogicException($invalidFormatMessage);
        }
        $deprecationPolicy['use_method'] = $methodWithoutVersion;
        $methodVersion = str_replace($methodWithoutVersion, '', $methodName);
        $deprecationPolicy['use_version'] = ucfirst($methodVersion);
    }
}
