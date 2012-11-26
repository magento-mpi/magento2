<?php
use Zend\Server\Reflection\ReflectionMethod;
/**
 * REST routes generator.
 *
 * @copyright {}
 */
class Mage_Webapi_Model_Config_Reader_RouteGenerator
{
    /**
     * @var Mage_Webapi_Helper_Data
     */
    protected $_helper;

    public function __construct(Mage_Webapi_Helper_Data $helper)
    {
        $this->_helper = $helper;
    }

    /**
     * Generate a list of routes available fo the specified method.
     *
     * @param ReflectionMethod $methodReflection
     * @return array
     */
    public function generateRestRoutes(ReflectionMethod $methodReflection)
    {
        $routes = array();
        $routePath = "/:" . Mage_Webapi_Controller_Router_Route_Rest::PARAM_VERSION;
        $routeParts = $this->_helper->getResourceNameParts($methodReflection->getDeclaringClass()->getName());
        $partsCount = count($routeParts);
        for ($i = 0; $i < $partsCount; $i++) {
            if ($this->_isParentResourceIdExpected($methodReflection)
                /**
                 * In case of subresource route, parent ID must be specified before the last route part.
                 * E.g.: /v1/grandParent/parent/:parentId/resource
                 */
                && ($i == ($partsCount - 1))
            ) {
                $routePath .= "/:" . Mage_Webapi_Controller_Router_Route_Rest::PARAM_PARENT_ID;
            }
            $routePath .= "/" . lcfirst($this->_helper->convertSingularToPlural($routeParts[$i]));
        }
        if ($this->_isResourceIdExpected($methodReflection)) {
            $routePath .= "/:" . Mage_Webapi_Controller_Router_Route_Rest::PARAM_ID;
        }

        foreach ($this->_getAdditionalRequiredParamNames($methodReflection) as $additionalRequired) {
            $routePath .= "/$additionalRequired/:$additionalRequired";
        }

        $actionType = $this->_helper->getActionTypeByMethod(
            $this->_helper->getMethodNameWithoutVersionSuffix($methodReflection)
        );
        $resourceName = $this->_helper->translateResourceName($methodReflection->getDeclaringClass()->getName());
        $optionalParams = $this->_getOptionalParamNames($methodReflection);
        foreach ($this->_getPathCombinations($optionalParams, $routePath) as $finalRoutePath) {
            $routes[$finalRoutePath] = array('actionType' => $actionType, 'resourceName' => $resourceName);
        }

        return $routes;
    }



    /**
     * Identify if method expects Parent resource ID to be present in the request.
     *
     * @param Zend\Server\Reflection\ReflectionMethod $methodReflection
     * @return bool
     */
    protected function _isParentResourceIdExpected(ReflectionMethod $methodReflection)
    {
        $isIdFieldExpected = false;
        if ($this->_isSubresource($methodReflection)) {
            $methodsWithParentId = array(
                Mage_Webapi_Controller_ActionAbstract::METHOD_CREATE,
                Mage_Webapi_Controller_ActionAbstract::METHOD_LIST,
                Mage_Webapi_Controller_ActionAbstract::METHOD_MULTI_UPDATE,
                Mage_Webapi_Controller_ActionAbstract::METHOD_MULTI_DELETE,
                Mage_Webapi_Controller_ActionAbstract::METHOD_MULTI_CREATE,
            );
            $methodName = $this->_helper->getMethodNameWithoutVersionSuffix($methodReflection);
            if (in_array($methodName, $methodsWithParentId)) {
                $isIdFieldExpected = true;
            }
        }
        return $isIdFieldExpected;
    }

    /**
     * Identify if method expects Resource ID to be present in the request.
     *
     * @param Zend\Server\Reflection\ReflectionMethod $methodReflection
     * @return bool
     */
    protected function _isResourceIdExpected(ReflectionMethod $methodReflection)
    {
        $isIdFieldExpected = false;
        $methodsWithId = array(
            Mage_Webapi_Controller_ActionAbstract::METHOD_GET,
            Mage_Webapi_Controller_ActionAbstract::METHOD_UPDATE,
            Mage_Webapi_Controller_ActionAbstract::METHOD_DELETE,
        );
        $methodName = $this->_helper->getMethodNameWithoutVersionSuffix($methodReflection);
        if (in_array($methodName, $methodsWithId)) {
            $isIdFieldExpected = true;
        }
        return $isIdFieldExpected;
    }

    /**
     * Identify if API resource is top level resource or subresource.
     *
     * @param ReflectionMethod $methodReflection
     * @return bool
     * @throws InvalidArgumentException In case when class name is not valid API resource class.
     */
    protected function _isSubresource(ReflectionMethod $methodReflection)
    {
        $className = $methodReflection->getDeclaringClass()->getName();
        if (preg_match(Mage_Webapi_Model_Config_Reader::RESOURCE_CLASS_PATTERN, $className, $matches)) {
            return count(explode('_', trim($matches[3], '_'))) > 1;
        }
        throw new InvalidArgumentException(sprintf('"%s" is not a valid resource class.', $className));
    }

    /**
     * Retrieve the list of names of required params except ID and Request body.
     *
     * @param ReflectionMethod $methodReflection
     * @return array
     */
    protected function _getAdditionalRequiredParamNames(ReflectionMethod $methodReflection)
    {
        $paramNames = array();
        $methodInterfaces = $methodReflection->getPrototypes();
        /** Take the fullest interface that includes optional parameters also. */
        /** @var \Zend\Server\Reflection\Prototype $methodInterface */
        $methodInterface = end($methodInterfaces);
        $methodParams = $methodInterface->getParameters();
        $idParamName = $this->getIdParamName($methodReflection);
        $bodyParamName = $this->getBodyParamName($methodReflection);
        /** @var ReflectionParameter $paramReflection */
        foreach ($methodParams as $paramReflection) {
            if (!$paramReflection->isOptional()
                && $paramReflection->getName() != $bodyParamName
                && $paramReflection->getName() != $idParamName
            ) {
                $paramNames[] = $paramReflection->getName();
            }
        }
        return $paramNames;
    }

    /**
     * Identify request body param name, if it is expected by method.
     *
     * @param ReflectionMethod $methodReflection
     * @return bool|string Return body param name if body is expected, false otherwise
     * @throws LogicException
     */
    public function getBodyParamName(ReflectionMethod $methodReflection)
    {
        $bodyParamName = false;
        /**#@+
         * Body param position in case of top level resources.
         */
        $bodyPosCreate = 1;
        $bodyPosMultiCreate = 1;
        $bodyPosUpdate = 2;
        $bodyPosMultiUpdate = 1;
        $bodyPosMultiDelete = 1;
        /**#@-*/
        $bodyParamPositions = array(
            Mage_Webapi_Controller_ActionAbstract::METHOD_CREATE => $bodyPosCreate,
            Mage_Webapi_Controller_ActionAbstract::METHOD_MULTI_CREATE => $bodyPosMultiCreate,
            Mage_Webapi_Controller_ActionAbstract::METHOD_UPDATE => $bodyPosUpdate,
            Mage_Webapi_Controller_ActionAbstract::METHOD_MULTI_UPDATE => $bodyPosMultiUpdate,
            Mage_Webapi_Controller_ActionAbstract::METHOD_MULTI_DELETE => $bodyPosMultiDelete
        );
        $methodName = $this->_helper->getMethodNameWithoutVersionSuffix($methodReflection);
        $isBodyExpected = isset($bodyParamPositions[$methodName]);
        if ($isBodyExpected) {
            $bodyParamPosition = $bodyParamPositions[$methodName];
            if ($this->_isSubresource($methodReflection)
                && $methodName != Mage_Webapi_Controller_ActionAbstract::METHOD_UPDATE
            ) {
                /** For subresources parent ID param must precede request body param. */
                $bodyParamPosition++;
            }
            $methodInterfaces = $methodReflection->getPrototypes();
            /** @var \Zend\Server\Reflection\Prototype $methodInterface */
            $methodInterface = reset($methodInterfaces);
            $methodParams = $methodInterface->getParameters();
            if (empty($methodParams) || (count($methodParams) < $bodyParamPosition)) {
                throw new LogicException(sprintf(
                    'Method "%s" must have parameter for passing request body. '
                        . 'Its position must be "%s" in method interface.',
                    $methodReflection->getName(),
                    $bodyParamPosition
                ));
            }
            /** @var $bodyParamReflection \Zend\Code\Reflection\ParameterReflection */
            /** Param position in the array should be counted from 0. */
            $bodyParamReflection = $methodParams[$bodyParamPosition - 1];
            $bodyParamName = $bodyParamReflection->getName();
        }
        return $bodyParamName;
    }

    /**
     * Identify ID param name if it is expected for the specified method.
     *
     * @param ReflectionMethod $methodReflection
     * @return bool|string Return ID param name if it is expected; false otherwise.
     * @throws LogicException If resource method interface does not contain required ID parameter.
     */
    public function getIdParamName(ReflectionMethod $methodReflection)
    {
        $idParamName = false;
        $isIdFieldExpected = false;
        if (!$this->_isSubresource($methodReflection)) {
            /** Top level resource, not subresource */
            $methodsWithId = array(
                Mage_Webapi_Controller_ActionAbstract::METHOD_GET,
                Mage_Webapi_Controller_ActionAbstract::METHOD_UPDATE,
                Mage_Webapi_Controller_ActionAbstract::METHOD_DELETE,
            );
            $methodName = $this->_helper->getMethodNameWithoutVersionSuffix($methodReflection);
            if (in_array($methodName, $methodsWithId)) {
                $isIdFieldExpected = true;
            }
        } else {
            /**
             * All subresources must have ID field:
             * either subresource ID (for item operations) or parent resource ID (for collection operations)
             */
            $isIdFieldExpected = true;
        }

        if ($isIdFieldExpected) {
            /** ID field must always be the first parameter of resource method */
            $methodInterfaces = $methodReflection->getPrototypes();
            /** @var \Zend\Server\Reflection\Prototype $methodInterface */
            $methodInterface = reset($methodInterfaces);
            $methodParams = $methodInterface->getParameters();
            if (empty($methodParams)) {
                throw new LogicException(sprintf(
                    'The "%s" method must have at least one parameter: resource ID.',
                    $methodReflection->getName()
                ));
            }
            /** @var ReflectionParameter $idParam */
            $idParam = reset($methodParams);
            $idParamName = $idParam->getName();
        }
        return $idParamName;
    }


    /**
     * Generate list of possible routes taking into account optional params.
     *
     * Note: this is called recursively.
     *
     * @param array $optionalParams
     * @param string $basePath
     * @return array List of possible route params
     */
    /**
     * TODO: Assure that performance is not heavily impacted during routes match process.
     * It can happen due creation of routes with optional parameters. HTTP get parameters can be used for that.
     */
    protected function _getPathCombinations($optionalParams, $basePath)
    {
        $pathCombinations = array();
        /** Add current base path to the resulting array of routes. */
        $pathCombinations[] = $basePath;
        foreach ($optionalParams as $key => $paramName) {
            /** Add current param name to the route path and make recursive call. */
            $paramsWithoutCurrent = $optionalParams;
            unset($paramsWithoutCurrent[$key]);
            $currentPath = "$basePath/$paramName/:$paramName";
            $pathCombinations = array_merge(
                $pathCombinations,
                $this->_getPathCombinations(
                    $paramsWithoutCurrent,
                    $currentPath
                )
            );
        }
        return $pathCombinations;
    }

    /**
     * Retrieve all optional parameters names.
     *
     * @param ReflectionMethod $methodReflection
     * @return array
     */
    protected function _getOptionalParamNames(ReflectionMethod $methodReflection)
    {
        $optionalParamNames = array();
        $methodInterfaces = $methodReflection->getPrototypes();
        /** Take the fullest interface that includes optional parameters also. */
        /** @var \Zend\Server\Reflection\Prototype $methodInterface */
        $methodInterface = end($methodInterfaces);
        $methodParams = $methodInterface->getParameters();
        /** @var ReflectionParameter $paramReflection */
        foreach ($methodParams as $paramReflection) {
            if ($paramReflection->isOptional()) {
                $optionalParamNames[] = $paramReflection->getName();
            }
        }
        return $optionalParamNames;
    }
}
