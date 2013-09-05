<?php
/**
 * Primary application config (app/etc/*.xml)
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_Config_Primary extends Magento_Core_Model_Config_Base
{
    /**
     * Install date xpath
     */
    const XML_PATH_INSTALL_DATE = 'global/install/date';

    /**
     * Configuration template for the application installation date
     */
    const CONFIG_TEMPLATE_INSTALL_DATE = '<config><global><install><date>%s</date></install></global></config>';

    /**
     * Application installation timestamp
     *
     * @var int|null
     */
    protected $_installDate;

    /**
     * @var Magento_Core_Model_Config_Loader_Primary
     */
    protected $_loader;

    /**
     * Application parameter list
     *
     * @var array
     */
    protected $_params;

    /**
     * Directory list
     *
     * @var Magento_Core_Model_Dir
     */
    protected $_dir;

    /**
     * @param string $baseDir
     * @param array $params
     * @param Magento_Core_Model_Dir $dir
     * @param Magento_Core_Model_Config_LoaderInterface $loader
     */
    public function __construct(
        $baseDir, array $params,
        Magento_Core_Model_Dir $dir = null,
        Magento_Core_Model_Config_LoaderInterface $loader = null
    ) {
        parent::__construct('<config/>');
        $this->_params = $params;
        $this->_dir = $dir ?: new Magento_Core_Model_Dir(
            $baseDir,
            $this->getParam(Mage::PARAM_APP_URIS, array()),
            $this->getParam(Mage::PARAM_APP_DIRS, array())
        );
        \Magento\Autoload\IncludePath::addIncludePath(array(
            $this->_dir->getDir(Magento_Core_Model_Dir::GENERATION)
        ));

        $this->_loader = $loader ?: new Magento_Core_Model_Config_Loader_Primary(
            new Magento_Core_Model_Config_Loader_Local(
                $this->_dir->getDir(Magento_Core_Model_Dir::CONFIG),
                $this->getParam(Mage::PARAM_CUSTOM_LOCAL_CONFIG),
                $this->getParam(Mage::PARAM_CUSTOM_LOCAL_FILE)
            ),
            $this->_dir->getDir(Magento_Core_Model_Dir::CONFIG)
        );
        $this->_loader->load($this);
        $this->_loadInstallDate();
    }

    /**
     * Get init param
     *
     * @param string $name
     * @param mixed $defaultValue
     * @return mixed
     */
    public function getParam($name, $defaultValue = null)
    {
        return isset($this->_params[$name]) ? $this->_params[$name] : $defaultValue;
    }

    /**
     * Get application init params
     *
     * @return array
     */
    public function getParams()
    {
        return $this->_params;
    }

    /**
     * Load application installation date
     */
    protected function _loadInstallDate()
    {
        $installDateNode = $this->getNode(self::XML_PATH_INSTALL_DATE);
        if ($installDateNode) {
            $this->_installDate = strtotime((string)$installDateNode);
        }
    }

    /**
     * Retrieve application installation date as a timestamp or NULL, if it has not been installed yet
     *
     * @return int|null
     */
    public function getInstallDate()
    {
        return $this->_installDate;
    }

    /**
     * Retrieve directories
     *
     * @return Magento_Core_Model_Dir
     */
    public function getDirectories()
    {
        return $this->_dir;
    }

    /**
     * Reinitialize primary configuration
     */
    public function reinit()
    {
        $this->loadString('<config/>');
        $this->_loader->load($this);
        $this->_loadInstallDate();
    }

    /**
     * Retrieve class definition config
     *
     * @return string
     */
    public function getDefinitionPath()
    {
        $pathInfo = (array) $this->getNode('global/di/definitions');
        if (isset($pathInfo['path'])) {
            return $pathInfo['path'];
        } else if (isset($pathInfo['relativePath'])) {
            return $this->_dir->getDir(Magento_Core_Model_Dir::ROOT) . DIRECTORY_SEPARATOR . $pathInfo['relativePath'];
        } else {
            return $this->_dir->getDir(Magento_Core_Model_Dir::DI);
        }
    }

    /**
     * Retrieve definition format
     *
     * @return string
     */
    public function getDefinitionFormat()
    {
        return (string) $this->getNode('global/di/definitions/format');
    }

    /**
     * Configure object manager
     *
     * Magento_Core_Model_ObjectManager $objectManager
     */
    public function configure(Magento_Core_Model_ObjectManager $objectManager)
    {
        \Magento\Profiler::start('initial');

        $objectManager->configure(array(
            'Magento_Core_Model_Config_Loader_Local' => array(
                'parameters' => array(
                    'configDirectory' => $this->_dir->getDir(Magento_Core_Model_Dir::CONFIG),
                )
            ),
            'Magento_Core_Model_Cache_Frontend_Factory' => array(
                'parameters' => array(
                    'decorators' => $this->_getCacheFrontendDecorators(),
                )
            ),
        ));

        $dynamicConfigurators = $this->getNode('global/configurators');
        if ($dynamicConfigurators) {
            $dynamicConfigurators = $dynamicConfigurators->asArray();
            if (count($dynamicConfigurators)) {
                foreach ($dynamicConfigurators as $configuratorClass) {
                    /** @var $dynamicConfigurator Magento_Core_Model_ObjectManager_DynamicConfigInterface*/
                    $dynamicConfigurator = $objectManager->create($configuratorClass);
                    $objectManager->configure($dynamicConfigurator->getConfiguration());
                }
            }
        }
        \Magento\Profiler::stop('initial');
    }

    /**
     * Retrieve cache frontend decorators configuration
     *
     * @return array
     */
    protected function _getCacheFrontendDecorators()
    {
        $result = array();
        // mark all cache entries with a special tag to be able to clean only cache belonging to the application
        $result[] = array(
            'class' => '\Magento\Cache\Frontend\Decorator\TagScope',
            'parameters' => array('tag' => 'MAGE'),
        );
        if (\Magento\Profiler::isEnabled()) {
            $result[] = array(
                'class' => '\Magento\Cache\Frontend\Decorator\Profiler',
                'parameters' => array('backendPrefixes' => array('Zend_Cache_Backend_', 'Magento_Cache_Backend_')),
            );
        }
        return $result;
    }
}
