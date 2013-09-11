<?php
/**
 * Module configuration file reader
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Config\Modules;

class Reader
{
    /**
     * Module configuration directories
     *
     * @var array
     */
    protected $_moduleDirs = array();

    /**
     * \Directory registry
     *
     * @var \Magento\Core\Model\Dir
     */
    protected $_dirs;

    /**
     * Modules configuration provider
     *
     * @var \Magento\Core\Model\ModuleListInterface
     */
    protected $_modulesList;

    /**
     * Base config factory
     *
     * @var \Magento\Core\Model\Config\BaseFactory
     */
    protected $_prototypeFactory;

    /**
     * @param \Magento\Core\Model\Dir $dirs
     * @param \Magento\Core\Model\Config\BaseFactory $prototypeFactory
     * @param \Magento\Core\Model\ModuleListInterface $moduleList
     */
    public function __construct(
        \Magento\Core\Model\Dir $dirs,
        \Magento\Core\Model\Config\BaseFactory $prototypeFactory,
        \Magento\Core\Model\ModuleListInterface $moduleList
    ) {
        $this->_dirs = $dirs;
        $this->_prototypeFactory = $prototypeFactory;
        $this->_modulesList = $moduleList;
    }

    /**
     * Load configuration from single file
     *
     * @param string $configFile
     * @param string $moduleName
     * @param \Magento\Core\Model\Config\Base $mergeToObject
     * @param \Magento\Core\Model\Config\Base $mergeModel
     */
    public function _loadFileConfig($configFile, $moduleName, $mergeToObject, $mergeModel)
    {
        $configFilePath = $this->getModuleDir('etc', $moduleName) . DS . $configFile;
        if ($mergeModel->loadFile($configFilePath)) {
            $mergeToObject->extend($mergeModel, true);
        }
    }

    /**
     * Iterate all active modules "etc" folders and combine data from
     * specified xml file name to one object
     *
     * @param string $fileName
     * @param \Magento\Core\Model\Config\Base|null $mergeToObject
     * @param \Magento\Core\Model\Config\Base|null $mergeModel
     * @return \Magento\Core\Model\Config\Base|null
     */
    public function loadModulesConfiguration($fileName, $mergeToObject = null, $mergeModel = null)
    {
        $mergeToObject = null === $mergeToObject ? $this->_prototypeFactory->create('<config/>') : $mergeToObject;
        $mergeModel = null === $mergeModel ? $mergeModel = $this->_prototypeFactory->create('<config/>'): $mergeModel;

        /** @var $module \Magento\Core\Model\Config\Element */
        foreach (array_keys($this->_modulesList->getModules()) as $moduleName) {
            if (!is_array($fileName)) {
                $fileName = array($fileName);
            }
            foreach ($fileName as $configFile) {
                $this->_loadFileConfig($configFile, $moduleName, $mergeToObject, $mergeModel);
            }

        }
        return $mergeToObject;
    }

    /**
     * Go through all modules and find configuration files of active modules
     *
     * @param $filename
     * @return array
     */
    public function getConfigurationFiles($filename)
    {
        $result = array();
        foreach (array_keys($this->_modulesList->getModules()) as $moduleName) {
            $file = $this->getModuleDir('etc', $moduleName) . DIRECTORY_SEPARATOR . $filename;
            if (file_exists($file)) {
                $result[] = $file;
            }
        }
        return $result;
    }

    /**
     * Get module directory by directory type
     *
     * @param string $type
     * @param string $moduleName
     * @return string
     */
    public function getModuleDir($type, $moduleName)
    {
        if (isset($this->_moduleDirs[$moduleName][$type])) {
            return $this->_moduleDirs[$moduleName][$type];
        }

        $dir = $this->_dirs->getDir(\Magento\Core\Model\Dir::MODULES) . DIRECTORY_SEPARATOR
            . uc_words($moduleName, DIRECTORY_SEPARATOR);

        switch ($type) {
            case 'etc':
            case 'controllers':
            case 'sql':
            case 'data':
            case 'locale':
            case 'view':
                $dir .= DS . $type;
                break;
        }

        $dir = str_replace('/', DS, $dir);
        return $dir;
    }

    /**
     * Set path to the corresponding module directory
     *
     * @param string $moduleName
     * @param string $type directory type (etc, controllers, locale etc)
     * @param string $path
     */
    public function setModuleDir($moduleName, $type, $path)
    {
        if (!isset($this->_moduleDirs[$moduleName])) {
            $this->_moduleDirs[$moduleName] = array();
        }
        $this->_moduleDirs[$moduleName][$type] = $path;
    }
}
