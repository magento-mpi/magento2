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
namespace Magento\View;

class Config implements \Magento\View\ConfigInterface
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
     * @var \Magento\Config\FileResolverInterface
     */
    protected $_moduleReader;

    /**
     * @var \Magento\Filesystem
     */
    protected $_filesystem;

    /**
     * @var \Magento\View\Service
     */
    protected $_viewService;

    /**
     * View file system model
     *
     * @var \Magento\View\FileSystem
     */
    protected $_viewFileSystem;

    /**
     * View config model
     *
     * @param \Magento\Config\FileResolverInterface $moduleReader
     * @param \Magento\Filesystem $filesystem
     * @param Service $viewService
     * @param FileSystem $viewFileSystem
     */
    public function __construct(
        \Magento\Config\FileResolverInterface $moduleReader,
        \Magento\Filesystem $filesystem,
        \Magento\View\Service $viewService,
        \Magento\View\FileSystem $viewFileSystem
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
     * @return \Magento\Config\View
     */
    public function getViewConfig(array $params = array())
    {
        $this->_viewService->updateDesignParams($params);
        /** @var $currentTheme \Magento\View\Design\ThemeInterface */
        $currentTheme = $params['themeModel'];
        $key = $currentTheme->getId();
        if (isset($this->_viewConfigs[$key])) {
            return $this->_viewConfigs[$key];
        }

        $configFiles = $this->_moduleReader->get(\Magento\Core\Model\Theme::FILENAME_VIEW_CONFIG, 'global');
        $themeConfigFile = $currentTheme->getCustomization()->getCustomViewConfigPath();
        if (empty($themeConfigFile) || !$this->_filesystem->has($themeConfigFile)) {
            $themeConfigFile = $this->_viewFileSystem->getFilename(
                \Magento\Core\Model\Theme::FILENAME_VIEW_CONFIG, $params
            );
        }
        if ($themeConfigFile && $this->_filesystem->has($themeConfigFile)) {
            $configFiles[] = $themeConfigFile;
        }
        $config = new \Magento\Config\View($configFiles);

        $this->_viewConfigs[$key] = $config;
        return $config;
    }
}
