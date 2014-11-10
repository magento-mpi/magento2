<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tools\Di\App;

use Magento\Framework\App;
use Zend\Code\Scanner\FileScanner;

class Compiler implements \Magento\Framework\AppInterface
{
    /**
     * @var \Magento\Framework\ObjectManager\Config
     */
    protected $config;

    /**
     * @param \Magento\Framework\ObjectManager\Config $config
     */
    public function __construct(
        \Magento\Framework\ObjectManager\Config $config,
        \Magento\Framework\App\AreaList $areaList,
        \Magento\Framework\App\ObjectManager\ConfigLoader $configLoader
    ) {
        $this->config = $config;
        $this->areaList = $areaList;
        $this->configLoader = $configLoader;
    }

    /**
     * Returns array of
     * ['class-name'] => [
     *      0 => [
     *          0 => , // string: Parameter name
     *          1 => , // string|null: Parameter type
     *          2 => , // bool: whether this param is required
     *          3 => , // mixed: default value
     *      ]
     * ]
     *
     * @param string $path
     * @return array
     */
    protected function getClasses($path)
    {
        $rdi = new \RecursiveDirectoryIterator(realpath($path));
        $recursiveIterator = new \RecursiveIteratorIterator($rdi, 1);
        $definitions = [];
        $signatureReader = new \Magento\Framework\Code\Reader\ClassReader();
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
                        $definitions[$className] = $signatureReader->getConstructor($className);
                    } catch (\Magento\Framework\Code\ValidationException $exception) {
                        $this->_log->add(Log::COMPILATION_ERROR, $className, $exception->getMessage());
                    } catch (\ReflectionException $e) {
                        $this->_log->add(Log::COMPILATION_ERROR, $className, $e->getMessage());
                    }
                }
            }
        }
        return $definitions;
    }

    /**
     * @param $classes
     * @param \Magento\Framework\ObjectManager\Config $config
     * @return array|mixed
     * @throws \ReflectionException
     */
    protected function getConfigForScope($classes, $config)
    {
        $arguments = array();
        $signatureReader = new \Magento\Framework\Code\Reader\ClassReader();
        foreach ($classes as $class => $constructor) {
            $refl = new \ReflectionClass($class);
            if ($refl->isInterface() || $refl->isAbstract()) {
                continue;
            }
            $arguments = $this->processConstructor($config, $class, $constructor, $arguments);
        }
        foreach (array_keys($config->getVirtualTypes()) as $type) {
            $originalType = $config->getInstanceType($type);
            if (!isset($classes[$originalType])) {
                $refl = new \ReflectionClass($originalType);
                if ($refl->isInterface() || $refl->isAbstract()) {
                    continue;
                }
                $constructor = $signatureReader->getConstructor($originalType);
            } else {
                $constructor = $classes[$originalType];
            }
            $arguments = $this->processConstructor($config, $type, $constructor, $arguments);
        }
        return $arguments;
    }

    /**
     * Launch application
     *
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function launch()
    {
        $paths = ['app/code', 'lib/internal/Magento/Framework', 'var/generation'];
        $classes = [];
        foreach ($paths as $path) {
            $classes = array_merge($this->getClasses(BP . '/' . $path), $classes);
        }
        if (!file_exists(BP . '/var/di')) {
            mkdir(BP . '/var/di');
        }
        $globalConfig = clone $this->config;
        $config['arguments'] = $this->getConfigForScope($classes, $globalConfig);
        foreach ($classes as $class => $constructor) {
            if (!$globalConfig->isShared($class)) {
                $config['nonShared'][$class] = true;
            }
            $preference = $globalConfig->getPreference($class);
            if ($class !== $preference) {
                $config['preferences'][$class] = $preference;
            }
        }
        foreach (array_keys($globalConfig->getVirtualTypes()) as $virtualType) {
            $config['instanceTypes'][$virtualType] = $globalConfig->getInstanceType($virtualType);
        }
        $serialized = serialize($config);
        file_put_contents(BP . '/var/di/config.ser',  $serialized);

        foreach ($this->areaList->getCodes() as $areaCode) {
            $config = [];
            $areaConfig = clone $this->config;
            $areaConfig->extend($this->configLoader->load($areaCode));
            $config['arguments'] = $this->getConfigForScope($classes, $areaConfig);
            foreach ($classes as $class => $constructor) {
                if (!$areaConfig->isShared($class)) {
                    $config['nonShared'][$class] = true;
                }
                $preference = $areaConfig->getPreference($class);
                if ($class !== $preference) {
                    $config['preferences'][$class] = $preference;
                }
            }
            foreach (array_keys($areaConfig->getVirtualTypes()) as $virtualType) {
                $config['instanceTypes'][$virtualType] = $areaConfig->getInstanceType($virtualType);
            }
            $serialized = serialize($config);
            file_put_contents(BP . '/var/di/' . $areaCode . '.ser',  $serialized);
        }
        $response = new \Magento\Framework\App\Console\Response();
        $response->setCode(0);
        return $response;
    }

    /**
     * Ability to handle exceptions that may have occurred during bootstrap and launch
     *
     * Return values:
     * - true: exception has been handled, no additional action is needed
     * - false: exception has not been handled - pass the control to Bootstrap
     *
     * @param App\Bootstrap $bootstrap
     * @param \Exception $exception
     * @return bool
     */
    public function catchException(App\Bootstrap $bootstrap, \Exception $exception)
    {
        echo $exception->getMessage();
    }

    /**
     * @param \Magento\Framework\ObjectManager\Config $config
     * @param $class
     * @param $constructor
     * @param $arguments
     * @return mixed
     */
    protected function processConstructor($config, $class, $constructor, $arguments)
    {
        $configuredArguments = $config->getArguments($class);
        $configuredArguments = array_map(
            function ($type) {
                if (isset($type['instance'])) {
                    $type['instance'] = ltrim($type['instance'], '\\');
                }

                return $type;
            },
            $configuredArguments
        );
        if ($constructor) {
            foreach ($constructor as $parameter) {
                $argument = ['__val__' => null];
                list ($paramName, $paramType, $paramRequired, $paramDefault) = $parameter;
                if (isset($configuredArguments[$paramName])) {
                    if ($paramType) {
                        if ($config->isShared($configuredArguments[$paramName]['instance'])) {
                            $argument = $configuredArguments[$paramName]['instance'];
                        } else {
                            $argument = [
                                '__non_shared__' => true,
                                '__instance__' => $configuredArguments[$paramName]['instance']
                            ];
                        }
                    } else {
                        if (isset($configuredArguments[$paramName]['argument'])) {
                            $argument = [
                                '__arg__' => $configuredArguments[$paramName]['argument'],
                                '__default__' => $paramDefault
                            ];
                        } else {
                            $argument = ['__val__' => $configuredArguments[$paramName]];
                        }
                    }
                } else {
                    if ($paramType) {
                        if (!$paramRequired) {
                            $argument = ['__val__' => $paramDefault];
                        } else {
                            if ($config->isShared($paramType)) {
                                $argument = $paramType;
                            } else {
                                $argument = ['__non_shared__' => true, '__instance__' => $paramType];
                            }
                        }
                    } else {
                        if (!$paramRequired) {
                            $argument = ['__val__' => $paramDefault];
                        }
                    }
                }
                $arguments[$class][$paramName] = $argument;
            }
            return $arguments;
        } else {
            $arguments[$class] = null;
            return $arguments;
        }
    }
} 
