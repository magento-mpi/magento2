<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Handles theme view.xml files
 */
class Magento_Core_Model_View_Config
{
    /**
     * List of view configuration objects per theme
     *
     * @var array
     */
    protected $_viewConfigs = array();

    /**
     * Module configuration reader
     *
     * @var Magento_Core_Model_Config_Modules_Reader
     */
    protected $_moduleReader;

    /**
     * @var Magento_Filesystem
     */
    protected $_filesystem;

    /**
     * @var Magento_Core_Model_View_Service
     */
    protected $_viewService;

    /**
     * View file system model
     *
     * @var Magento_Core_Model_View_FileSystem
     */
    protected $_viewFileSystem;

    /**
     * View config model
     *
     * @param Magento_Core_Model_Config_Modules_Reader $moduleReader
     * @param Magento_Filesystem $filesystem
     * @param Magento_Core_Model_View_Service $viewService
     * @param Magento_Core_Model_View_FileSystem $viewFileSystem
     */
    public function __construct(
        Magento_Core_Model_Config_Modules_Reader $moduleReader,
        Magento_Filesystem $filesystem,
        Magento_Core_Model_View_Service $viewService,
        Magento_Core_Model_View_FileSystem $viewFileSystem
    ) {
        $this->_moduleReader = $moduleReader;
        $this->_filesystem = $filesystem;
        $this->_viewService = $viewService;
        $this->_viewFileSystem = $viewFileSystem;
    }

    /**
     * Render view config object for current package and theme
     *
     * @param array $params
     * @return Magento_Config_View
     */
    public function getViewConfig(array $params = array())
    {
        $this->_viewService->updateDesignParams($params);
        /** @var $currentTheme Magento_Core_Model_Theme */
        $currentTheme = $params['themeModel'];
        $key = $currentTheme->getId();
        if (isset($this->_viewConfigs[$key])) {
            return $this->_viewConfigs[$key];
        }

        $configFiles = $this->_moduleReader->getConfigurationFiles(Magento_Core_Model_Theme::FILENAME_VIEW_CONFIG);
        $themeConfigFile = $currentTheme->getCustomization()->getCustomViewConfigPath();
        if (empty($themeConfigFile) || !$this->_filesystem->has($themeConfigFile)) {
            $themeConfigFile = $this->_viewFileSystem->getFilename(
                Magento_Core_Model_Theme::FILENAME_VIEW_CONFIG, $params
            );
        }
        if ($themeConfigFile && $this->_filesystem->has($themeConfigFile)) {
            $configFiles[] = $themeConfigFile;
        }
        $config = new Magento_Config_View($configFiles);

        $this->_viewConfigs[$key] = $config;
        return $config;
    }
}
