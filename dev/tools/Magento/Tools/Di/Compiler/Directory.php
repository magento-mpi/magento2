<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Di\Compiler;

use Zend\Code\Scanner\FileScanner;
use Magento\Tools\Di\Compiler\Log\Log;

class Directory
{
    /**
     * @var array
     */
    protected $_processedClasses = array();

    /**
     * @var array
     */
    protected $_definitions = array();

    /**
     * @var string
     */
    protected $_current;

    /**
     * @var Log
     */
    protected $_log;

    /**
     * @var array
     */
    protected $_relations;

    /**
     * @var  \Magento\Framework\Code\Validator
     */
    protected $_validator;

    protected $scopes = [
        'global', 'frontend', 'adminhtml', 'webapi_soap', 'webapi_rest', 'install', 'crontab'
    ];

    /**
     * @param Log $log
     * @param \Magento\Framework\Code\Validator $validator
     */
    public function __construct(Log $log, \Magento\Framework\Code\Validator $validator)
    {
        $this->_log = $log;
        $this->_validator = $validator;
        set_error_handler(array($this, 'errorHandler'), E_STRICT);
    }

    /**
     * @param int $errno
     * @param string $errstr
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function errorHandler($errno, $errstr)
    {
        $this->_log->add(Log::COMPILATION_ERROR, $this->_current, $errstr);
    }

    /**
     * Compile class definitions
     *
     * @param string $path
     * @param bool $validate
     * @return void
     */
    public function compile($path, $validate = true)
    {
        $rdi = new \RecursiveDirectoryIterator(realpath($path));
        $recursiveIterator = new \RecursiveIteratorIterator($rdi, 1);
        /** @var $item \SplFileInfo */
        foreach ($recursiveIterator as $item) {
            if ($item->isFile() && pathinfo($item->getRealPath(), PATHINFO_EXTENSION) == 'php') {
                $fileScanner = new FileScanner($item->getRealPath());
                $classNames = $fileScanner->getClassNames();
                foreach ($classNames as $className) {
                    $this->_current = $className;
                    if (!class_exists($className)) {
                        require_once $item->getRealPath();
                    }
                    try {
                        if ($validate) {
                            $this->_validator->validate($className);
                        }
                        $signatureReader = new \Magento\Framework\Code\Reader\ClassReader();
                        $this->_definitions[$className] = $signatureReader->getConstructor($className);
                        $this->_relations[$className] = $signatureReader->getParents($className);
                    } catch (\Magento\Framework\Code\ValidationException $exception) {
                        $this->_log->add(Log::COMPILATION_ERROR, $className, $exception->getMessage());
                    } catch (\ReflectionException $e) {
                        $this->_log->add(Log::COMPILATION_ERROR, $className, $e->getMessage());
                    }
                    $this->_processedClasses[$className] = 1;
                }
            }
        }
    }

    /**
     * Retrieve compilation result
     *
     * @return array
     */
    public function getResult()
    {
        return array($this->_definitions, $this->_relations);
    }

    /*********************************** Arguments processing ***********************************************/

    public function processInheritance(\Magento\Framework\App\ObjectManager\ConfigLoader  $configLoader)
    {
        $output = [];
        $defaultScope = 'global';
        foreach ($this->scopes as $scope) {
            $config = new \Magento\Framework\ObjectManager\Config\Config();
            if ($scope != $defaultScope) {
                $config->extend($configLoader->load($defaultScope));
            }
            $config->extend($configLoader->load($scope));
            foreach ($this->_definitions as $class => $constructorArguments) {
                $configuredArguments = $config->getArguments($class);
                $output[$scope][$class] = $this->_mapArguments($class, $config, $constructorArguments, $configuredArguments);
            }
        }

        foreach ($output as $scope => $definitions) {
            if ($scope != 'global') {
                $definitions = $this->getDefinitionsDiff($output['global'], $definitions);
            }
            $output[$scope] = $definitions;
        }
        return $output;
    }

    protected function getDefinitionsDiff($globalDefinitions, $scopeDefinitions)
    {
        $output = [];
        foreach ($globalDefinitions as $key => $val) {
            if ($val != $scopeDefinitions[$key]) {
                $output[$key] = $scopeDefinitions[$key];
            }
        }
        return $output;
    }


    const INSTANCE_KEY = '__inst__';
    const SHARED_KEY = '__shared__';
    const ARGUMENT_KEY = '__arg___';
    const VALUE_KEY = '__val___';
    /**
     * @param string $requestedType requested class name
     * @param \Magento\Framework\ObjectManager\Config\Config $config
     * @param array $parameters constructor parameters
     * @param array $arguments configured arguments
     * @return array
     */
    protected function _mapArguments(
        $requestedType, \Magento\Framework\ObjectManager\Config\Config $config, $parameters, $arguments
    ) {
        if (is_null($parameters)) {
            return $parameters;
        }
        $resolvedArguments = [];
        foreach ($parameters as $parameter) {
            $argumentConfig = null;
            list($paramName, $paramType, $paramRequired, $paramDefault) = $parameter;
            $argument = null;
            if (array_key_exists($paramName, $arguments)) {
                $argument = $arguments[$paramName];
            } elseif ($paramRequired) {
                if ($paramType) {
                    $argument = array('instance' => $paramType);
                } else {
                    $argumentConfig = null;
                }
            } else {
                $argument = $paramDefault;
            }

            if ($paramType && $argument !== $paramDefault) { //processing of arguments that are objects
                if (!is_array($argument) || !isset($argument['instance'])) {
                    throw new \UnexpectedValueException(
                        'Invalid parameter configuration provided for $' . $paramName . ' argument of ' . $requestedType
                    );
                }
                $argumentType = $argument['instance'];
                $isShared = (isset($argument['shared']) ? $argument['shared'] : $config->isShared($argumentType));

                if (!$isShared) {
                    $argumentConfig[self::INSTANCE_KEY] = $argumentType;
                    $argumentConfig[self::SHARED_KEY] = false;
                } else {
                    $argumentConfig = $argumentType;
                }
            } elseif (is_array($argument)) { //processing scalar arguments value
                if (isset($argument['argument'])) {
                    $argumentConfig[self::ARGUMENT_KEY] = $argument['argument'];
                } else if (!empty($argument)) {
                    $this->parseArray($argument, $config);
                    $argumentConfig[self::VALUE_KEY] = $argument;
                }
            }
            $resolvedArguments[$paramName] = $argumentConfig;
        }
        return $resolvedArguments;
    }

    /**
     * Parse array argument
     *
     * @param array $array
     * @return void
     */
    protected function parseArray(&$array, \Magento\Framework\ObjectManager\Config\Config $config)
    {
        foreach ($array as $key => $item) {
            if (is_array($item)) {
                if (isset($item['instance'])) {
                    $itemType = $item['instance'];
                    $isShared = (isset($item['shared'])) ? $item['shared'] : $config->isShared($itemType);
                    if (!$isShared) {
                        $array[$key] = [self::INSTANCE_KEY => $itemType, self::SHARED_KEY => false];
                    } else {
                        $array[$key] = $itemType;
                    }
                } elseif (isset($item['argument'])) {
                    $array[$key] = [self::ARGUMENT_KEY => $item['argument']];
                } else {
                    $this->parseArray($array[$key], $config);
                }
            }
        }
    }
}
