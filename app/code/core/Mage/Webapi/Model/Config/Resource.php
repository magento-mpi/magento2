<?php
use Zend\Code\Scanner\DirectoryScanner,
    Zend\Code\Reflection\ClassReflection,
    Zend\Server\Reflection;

/**
 * Magento API Resources config.
 *
 * @copyright {}
 */
class Mage_Webapi_Model_Config_Resource
{
    /**
     * @var DirectoryScanner
     */
    protected $_directoryScanner;

    /**
     * @var Magento_Autoload
     */
    protected $_autoloader;

    /**
     * @var Mage_Core_Model_Config
     */
    protected $_applicationConfig;

    /**
     * @var Reflection
     */
    protected $_serverReflection;

    /**
     * Resources configuration data.
     *
     * @var array
     */
    protected $_data;

    /**
     * Resources complex types
     *
     * @var array
     */
    protected $_types;

    /**
     * Class map for auto loader.
     *
     * @var array
     */
    protected $_classMap;


    /**
     * Initialize API resources config.
     *
     * @param array $options
     */
    public function __construct(array $options)
    {
        if (isset($options['directoryScanner']) && $options['directoryScanner'] instanceof DirectoryScanner) {
            $this->_directoryScanner = $options['directoryScanner'];
        } else {
            $directories = array();
            /** @var Mage_Core_Model_Config_Element $module */
            foreach (Mage::getConfig()->getNode('modules')->children() as $moduleName => $module) {
                if ($module->is('active')) {
                    $directory = Mage::getConfig()->getModuleDir('controllers', $moduleName) . DS . 'Webapi';
                    if (is_dir($directory)) {
                        $directories[] = $directory;
                    }
                }
            }
            $this->_directoryScanner = new DirectoryScanner($directories);
        }

        if (isset($options['autoloader']) && $options['autoloader'] instanceof Magento_Autoload) {
            $this->_autoloader = $options['autoloader'];
        } else {
            $this->_autoloader = Magento_Autoload::getInstance();
        }

        if (isset($options['applicationConfig']) && $options['applicationConfig'] instanceof Mage_Core_Model_Config) {
            $this->_applicationConfig = $options['applicationConfig'];
        } else {
            $this->_applicationConfig = Mage::getConfig();
        }

        if (isset($options['serverReflection']) && $options['serverReflection'] instanceof Reflection) {
            $this->_serverReflection = $options['serverReflection'];
        } else {
            $this->_serverReflection = new Reflection();
        }

        $this->_extractData();
    }

    /**
     * Retrieve method data for given resource name and method name.
     *
     * @param string $resourceName
     * @param string $methodName
     * @return array
     * @throws InvalidArgumentException
     */
    public function getResourceMethodData($resourceName, $methodName)
    {
        if (!array_key_exists($resourceName, $this->_data['resources'])) {
            throw new InvalidArgumentException(
                sprintf('Resource "%s" is not found in config.', $resourceName));
        }
        if (!array_key_exists($methodName, $this->_data['resources'][$resourceName])) {
            throw new InvalidArgumentException(
                sprintf('Method "%s" for resource "%s" is not found in config.', $methodName, $resourceName));
        }
        return $this->_data['resources'][$resourceName][$methodName];
    }

    /**
     * Retrieve data type details for the given type name.
     *
     * @param string $typeName
     * @return array
     * @throws InvalidArgumentException
     */
    public function getDataType($typeName)
    {
        if (!isset($this->_types[$typeName])) {
            throw new InvalidArgumentException(sprintf('Data type "%s" is not found in config.', $typeName));
        }
        return $this->_types[$typeName];
    }

    /**
     * Retrieve list of resources with methods
     *
     * @return array
     */
    public function getResources()
    {
        return $this->_data['resources'];
    }

    /**
     * Retrieve specific resource version interface data.
     *
     * @param string $resourceName
     * @param string $resourceVersion
     * @return array
     * @throws RuntimeException
     */
    public function getResource($resourceName, $resourceVersion)
    {
        $helper = Mage::helper('Mage_Webapi_Helper_Data');
        if (!isset($this->_data[$resourceName])) {
            throw new RuntimeException($helper->__('Unknown resource "%s".', $resourceName));
        }
        if (!isset($this->_data[$resourceName]['versions'][$resourceVersion])) {
            throw new RuntimeException($helper->__('Unknown version "%s" for resource "%s".', $resourceVersion,
                $resourceName));
        }

        $resource = array();
        foreach ($this->_data[$resourceName]['versions'] as $version => $data) {
            $resource = array_replace_recursive($resource, $data);
            if ($version == $resourceVersion) {
                break;
            }
        }

        return $resource;
    }

    /**
     * Identify resource name by operation name.
     * This method relies on convention that port type value equals to resource name
     *
     * @param string $operationName
     * @return string|bool Resource name on success; false on failure
     */
    public function getResourceNameByOperation($operationName)
    {
        return isset($this->_data['operations'][$operationName])
            ? $this->_data['operations'][$operationName]['resource_name']
            : false;
    }

    /**
     * Identify method name by operation name.
     *
     * @param string $operationName
     * @return string|bool Method name on success; false on failure
     */
    public function getMethodNameByOperation($operationName)
    {
        return isset($this->_data['operations'][$operationName])
            ? $this->_data['operations'][$operationName]['method_name']
            : false;
    }

    /**
     * Identify module name by operation name.
     *
     * @param string $operationName
     * @return string|bool Module name on success; false on failure
     */
    public function getModuleNameByOperation($operationName)
    {
        return isset($this->_data['operations'][$operationName])
            ? $this->_data['operations'][$operationName]['module_name']
            : false;
    }

    /**
     * Identify deprecation policy for the specified operation.
     *
     * Return result in the following format:<pre>
     * array(
     *     'deprecated' => true,              // either 'deprecated' or 'removed' item must be specified
     *     'removed' => true,
     *     'use_version' => N,                // version of operation to be used instead
     *     'use_operation' => 'operationName' // operation to be used instead
     * )
     * </pre>
     *
     * @param string $operationName
     * @return array|bool
     */
    public function getOperationDeprecationPolicy($operationName)
    {
        return isset($this->_data['operations'][$operationName])
            && isset($this->_data['operations'][$operationName]['deprecation_policy'])
            ? $this->_data['operations'][$operationName]['deprecation_policy']
            : false;
    }

    /**
     * Extract configuration data from the action controllers files.
     *
     * @throws InvalidArgumentException
     * @throws LogicException
     * @return array
     */
    protected function _extractData()
    {
        if (is_null($this->_data)) {
            $this->_populateClassMap();

            foreach ($this->_classMap as $className => $filename) {
                if (preg_match('/(.*)_Webapi_(.*)Controller*/', $className)) {
                    $data = array();
                    $data['controller'] = $className;
                    /** @var \Zend\Server\Reflection\ReflectionMethod $method */
                    foreach ($this->_serverReflection->reflectClass($className)->getMethods() as $method) {
                        $methodName = $method->getName();
                        $regEx = sprintf('/(%s)(V\d+)/', implode('|', $this->_getAllowedMethods()));
                        if (preg_match($regEx, $methodName, $methodMatches)) {
                            $operation = $methodMatches[1];
                            $version = lcfirst($methodMatches[2]);
                            $data['versions'][$version]['operations'][$operation] = $this->_getMethodData($method);
                        }
                    }
                    // Sort versions array for further fallback.
                    ksort($data['versions']);
                    $this->_data[$this->translateResourceName($className)] = $data;
                }
            }

            if (empty($this->_data)) {
                throw new InvalidArgumentException('Can not populate config - no action controllers were found.');
            }
        }

        return $this->_data;
    }

    /**
     * Walk all files from directory scanner and set them into autoloader class map.
     *
     * @throws LogicException
     */
    protected function _populateClassMap()
    {
        $classMap = array();
        /** @var \Zend\Code\Scanner\FileScanner $file */
        foreach ($this->_directoryScanner->getFiles(true) as $file) {
            $filename = $file->getFile();
            $classes = $file->getClasses();
            if (count($classes) > 1) {
                throw new LogicException(sprintf('There can be only one class in controller file "%s".', $filename));
            }
            /** @var \Zend\Code\Scanner\ClassScanner $class */
            $class = reset($classes);
            $relativePath = str_replace($this->_applicationConfig->getOptions()->getBaseDir(), '', $filename);
            $classMap[$class->getName()] = $relativePath;
        }

        $this->_classMap = $classMap;
        $this->_autoloader->addFilesMap($this->_classMap);
    }

    /**
     * Retrieve method interface and documentation description.
     *
     * @param Zend\Server\Reflection\ReflectionMethod $method
     * @return array
     * @throws InvalidArgumentException
     */
    protected function _getMethodData(\Zend\Server\Reflection\ReflectionMethod $method)
    {
        $methodData = array(
            'documentation' => $method->getDescription(),
        );

        // TODO: copy-past from Zend_Soap_AutoDiscover, review
        $prototype = null;
        $maxNumArgumentsOfPrototype = -1;
        /** @var \Zend\Server\Reflection\Prototype $tmpPrototype */
        foreach ($method->getPrototypes() as $tmpPrototype) {
            $numParams = count($tmpPrototype->getParameters());
            if ($numParams > $maxNumArgumentsOfPrototype) {
                $maxNumArgumentsOfPrototype = $numParams;
                $prototype = $tmpPrototype;
            }
        }
        if (is_null($prototype)) {
            throw new InvalidArgumentException(sprintf('No prototypes could be found for the "%s" function.',
                $method->getName()));
        }

        /** @var \Zend\Server\Reflection\ReflectionParameter $parameter */
        foreach ($prototype->getParameters() as $parameter) {
            $methodData['interface']['in']['parameters'][$parameter->getName()] = array(
                'type' => $this->_processType($parameter->getType()),
                'required' => !$parameter->isOptional(),
                'documentation' => $parameter->getDescription(),
            );
        }

        if ($prototype->getReturnType() != 'void') {
            $methodData['interface']['out']['result'] = array(
                'type' => $this->_processType($prototype->getReturnType()),
                'documentation' => $prototype->getReturnValue()->getDescription()
            );
        }

        return $methodData;
    }

    /**
     * Process type name.
     * In case parameter type is a complex type (class) - process it's properties.
     *
     * @param string $type
     * @param string $previouslyProcessedType
     * @return string
     */
    protected function _processType($type, $previouslyProcessedType = null)
    {
        $typeName = $this->normalizeType($type);
        if (!$this->isTypeSimple($typeName)) {
            $complexTypeName = $this->translateTypeName($type);
            if (!isset($this->_types[$complexTypeName])) {
                $this->_types[$complexTypeName] = $this->_processComplexType($type, $previouslyProcessedType);
            }
            $typeName = $complexTypeName;
        }

        return $typeName;
    }

    /**
     * Retrieve complex type information from class public properties.
     *
     * @param string $class
     * @param string $previouslyProcessedClass
     * @return array
     * @throws InvalidArgumentException
     */
    protected function _processComplexType($class, $previouslyProcessedClass = null)
    {
        $class = str_replace('[]', '', $class);
        if (!class_exists($class)) {
            throw new InvalidArgumentException(sprintf('Could not load class "%s" as parameter type.', $class));
        }

        $typeData = array();
        $reflection = new ClassReflection($class);
        $defaultProperties = $reflection->getDefaultProperties();
        /** @var \Zend\Code\Reflection\PropertyReflection $property */
        foreach ($reflection->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
            $propertyName = $property->getName();
            $doc = $property->getDocBlock();
            $tags = $doc->getTags('var');
            if (empty($tags)) {
                throw new InvalidArgumentException('Property type must be defined with @var tag.');
            }
            /** @var \Zend\Code\Reflection\DocBlock\Tag\GenericTag $varTag */
            $varTag = current($tags);
            $varType = $varTag->returnValue(0);
            $varTypeArrayClean = str_replace('[]', '', $varType);
            $propertyType = ($varTypeArrayClean == $class || $varTypeArrayClean == $previouslyProcessedClass)
                ? $this->translateTypeName($varType)
                : $this->_processType($varType, $class);
            $typeData[$propertyName] = array(
                'type' => $propertyType,
                'required' => is_null($defaultProperties[$propertyName]),
                'default' => $defaultProperties[$propertyName],
                'documentation' => $doc->getShortDescription() . $doc->getLongDescription()
            );
        }

        return $typeData;
    }

    /**
     * Translate controller class name into resource name.
     *
     * @param string $class
     * @return string
     * @throws InvalidArgumentException
     */
    public function translateResourceName($class)
    {
        if (preg_match('/(.*)_Webapi_(.*)Controller*/', $class, $matches)) {
            list($moduleNamespace, $moduleName) = explode('_', $matches[1]);
            $moduleNamespace = $moduleNamespace == 'Mage' ? '' : $moduleNamespace;

            $controllerNameParts = explode('_', $matches[2]);
            if ($moduleName == $controllerNameParts[0]) {
                array_shift($controllerNameParts);
            }

            return lcfirst($moduleNamespace . $moduleName . implode('', $controllerNameParts));
        }

        throw new InvalidArgumentException('Invalid controller class name.');
    }

    /**
     * Translate complex type class name into type name.
     *
     * @param string $class
     * @return string
     * @throws InvalidArgumentException
     */
    public function translateTypeName($class)
    {
        if (preg_match('/(.*)_(.*)_Webapi_(.*)/', $class, $matches)) {
            $moduleNamespace = $matches[1] == 'Mage' ? '' : $matches[1];
            $moduleName = $matches[2];
            $typeNameParts = explode('_', $matches[3]);
            if ($moduleName == $typeNameParts[0]) {
                array_shift($typeNameParts);
            }

            return lcfirst($moduleNamespace . $moduleName . implode('', $typeNameParts));
        }

        throw new InvalidArgumentException('Invalid parameter type.');
    }

    /**
     * Normalize short type names to full type names.
     *
     * @param string $type
     * @return string
     */
    public function normalizeType($type)
    {
        $normalizationMap = array(
            'str' => 'string',
            'int' => 'integer',
            'bool' => 'boolean',
        );

        return isset($normalizationMap[$type]) ? $normalizationMap[$type] : $type;
    }

    /**
     * Check if given type is a simple type.
     *
     * @param string $type
     * @return bool
     */
    public function isTypeSimple($type)
    {
        return in_array($type, array('string', 'integer', 'float', 'double', 'boolean', 'array'));
    }

    /**
     * Retrieve list of allowed method names in action controllers.
     *
     * @return array
     */
    protected function _getAllowedMethods()
    {
        return array(
            Mage_Webapi_Controller_ActionAbstract::METHOD_CREATE,
            Mage_Webapi_Controller_ActionAbstract::METHOD_RETRIEVE,
            Mage_Webapi_Controller_ActionAbstract::METHOD_LIST,
            Mage_Webapi_Controller_ActionAbstract::METHOD_UPDATE,
            Mage_Webapi_Controller_ActionAbstract::METHOD_MULTI_UPDATE,
            Mage_Webapi_Controller_ActionAbstract::METHOD_DELETE,
            Mage_Webapi_Controller_ActionAbstract::METHOD_MULTI_UPDATE,
        );
    }
}
