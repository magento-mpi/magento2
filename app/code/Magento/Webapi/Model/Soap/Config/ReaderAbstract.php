<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Webapi\Model\Soap\Config;

/**
 * Abstract config data reader.
 */
abstract class ReaderAbstract
{
    /**
     * Cache ID for resource config.
     */
    const CONFIG_CACHE_ID = 'API-RESOURCE-CACHE';

    /**
     * Pattern for service class name.
     */
    const SERVICE_CLASS_PATTERN = '/^(.*)\\\\(.*)\\\\Service(\\\\.*)+V(\d)+Interface$/';

    /**
     * @var \Zend\Code\Scanner\DirectoryScanner
     */
    protected $_directoryScanner;

    /**
     * @var \Magento\Webapi\Model\Soap\Config\Reader\ClassReflectorAbstract
     */
    protected $_classReflector;

    /**
     * @var \Magento\Module\Dir
     */
    protected $_moduleDir;

    /**
     * @var \Magento\Webapi\Model\Cache\Type
     */
    protected $_cache;

    /**
     * Modules configuration provider
     *
     * @var \Magento\Module\ModuleListInterface
     */
    protected $_modulesList;

    /**
     * @var array <pre>array(
     *     'services' => array(
     *         $serviceA => array(
     *             'service' => $interfaceName,
     *             'methods' => array(
     *                 $firstMethod => array(
     *                     'documentation' => $methodDocumentation,
     *                     'interface' => array(
     *                         'in' => array(
     *                             'parameters' => array(
     *                                 $firstParameter => array(
     *                                     'type' => $type,
     *                                     'required' => $isRequired,
     *                                     'documentation' => $parameterDocumentation
     *                                 ),
     *                                 ...
     *                             )
     *                         ),
     *                         'out' => array(
     *                             'parameters' => array(
     *                                 $firstParameter => array(
     *                                     'type' => $type,
     *                                     'required' => $isRequired,
     *                                     'documentation' => $parameterDocumentation
     *                                 ),
     *                                 ...
     *                             )
     *                         )
     *                     )
     *                 ),
     *                 ...
     *             )
     *         ),
     *         ...
     *     ),
     *     'types' => array(
     *         $complexTypeName => array(
     *             'documentation' => $typeDocumentation,
     *             'parameters' => array(
     *                 $firstParameter => array(
     *                     'type' => $parameterType,
     *                     'required' => $isRequired,
     *                     'default' => $defaultValue,
     *                     'documentation' => $parameterDocumentation
     *                 ),
     *                 ...
     *             )
     *         ),
     *         ...
     *     ),
     *     'type_to_class_map' => array(
     *         $complexTypeName => $interfaceName,
     *         ...
     *     )
     * )</pre>
     */
    protected $_data = array();

    /**
     * Construct config reader.
     *
     * @param \Magento\Webapi\Model\Soap\Config\Reader\ClassReflectorAbstract $classReflector
     * @param \Magento\Module\Dir $moduleDir
     * @param \Magento\Webapi\Model\Cache\Type $cache
     * @param \Magento\Module\ModuleListInterface $moduleList
     */
    public function __construct(
        \Magento\Webapi\Model\Soap\Config\Reader\ClassReflectorAbstract $classReflector,
        \Magento\Module\Dir $moduleDir,
        \Magento\Webapi\Model\Cache\Type $cache,
        \Magento\Module\ModuleListInterface $moduleList
    ) {
        $this->_classReflector = $classReflector;
        $this->_moduleDir = $moduleDir;
        $this->_cache = $cache;
        $this->_modulesList = $moduleList;
    }

    /**
     * Retrieve cache ID.
     *
     * @return string
     */
    abstract public function getCacheId();

    /**
     * Get current directory scanner. Initialize if it was not initialized previously.
     *
     * @return \Zend\Code\Scanner\DirectoryScanner
     */
    public function getDirectoryScanner()
    {
        if (!$this->_directoryScanner) {
            $this->_directoryScanner = new \Zend\Code\Scanner\DirectoryScanner();
            foreach (array_keys($this->_modulesList->getModules()) as $moduleName) {
                $directory = $this->_moduleDir->getDir($moduleName) . DS . 'Service';
                if (is_dir($directory)) {
                    $this->_directoryScanner->addDirectory($directory);
                }
            }
        }

        return $this->_directoryScanner;
    }

    /**
     * Set directory scanner object.
     *
     * @param \Zend\Code\Scanner\DirectoryScanner $directoryScanner
     */
    public function setDirectoryScanner(\Zend\Code\Scanner\DirectoryScanner $directoryScanner)
    {
        $this->_directoryScanner = $directoryScanner;
    }

    /**
     * Read configuration data from the service files using class reflector.
     *
     * @param array $declaredSoapServices
     * @return array
     * @throws \InvalidArgumentException
     * @throws \LogicException
     */
    public function getData($declaredSoapServices)
    {
        if (!$this->_data) {
            $cachedData = $this->_cache->load($this->getCacheId());
            if ($cachedData && is_string($cachedData)) {
                $this->_data = unserialize($cachedData);
            } else {
                /** @var \Zend\Code\Scanner\FileScanner $file */
                foreach ($this->getDirectoryScanner()->getFiles(true) as $file) {
                    $filename = $file->getFile();
                    $classes = $file->getClasses();
                    if (count($classes) > 1) {
                        throw new \LogicException(sprintf(
                            'There can be only one class declared in the "%s" service file.',
                            $filename
                        ));
                    }
                    /** @var \Zend\Code\Scanner\ClassScanner $class */
                    $class = current($classes);
                    $className = $class->getName();
                    if (preg_match(self::SERVICE_CLASS_PATTERN, $className)
                        && array_key_exists($className, $declaredSoapServices)
                    ) {
                        $classData = $this->_classReflector->reflectClassMethods(
                            $className,
                            $declaredSoapServices[$className]['methods']
                        );
                        $this->_addData($classData);
                    }
                }
                $postReflectionData = $this->_classReflector->getPostReflectionData();
                $this->_addData($postReflectionData);

                if (!isset($this->_data['services'])) {
                    throw new \LogicException('Cannot populate reflection data array - no services were found.');
                }

                $this->_cache->save(
                    serialize($this->_data),
                    $this->getCacheId(),
                    array(\Magento\Webapi\Model\Cache\Type::CACHE_TAG)
                );
            }
        }

        return $this->_data;
    }

    /**
     * Add data to reader.
     *
     * @param array $data
     */
    protected function _addData($data)
    {
        $this->_data = array_merge_recursive($this->_data, $data);
    }
}
