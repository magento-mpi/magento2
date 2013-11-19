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

use Magento\Filesystem\DirectoryList,
    Magento\Filesystem\Directory\Read;

class Config implements \Magento\View\ConfigInterface
{
    /**
     * List of view configuration objects per theme
     *
     * @var array
     */
    protected $viewConfigs = array();

    /**
     * Module configuration reader
     *
     * @var \Magento\Module\Dir\Reader
     */
    protected $moduleReader;

    /**
     * @var Read
     */
    protected $rootDirectory;

    /**
     * @var \Magento\View\Service
     */
    protected $viewService;

    /**
     * View file system model
     *
     * @var \Magento\View\FileSystem
     */
    protected $viewFileSystem;

    /**
     * @var string
     */
    protected $filename;

    /**
     * View config model
     *
     * @param \Magento\Module\Dir\Reader $moduleReader
     * @param \Magento\Filesystem $filesystem
     * @param Service $viewService
     * @param FileSystem $viewFileSystem
     * @param string $filename
     */
    public function __construct(
        \Magento\Module\Dir\Reader $moduleReader,
        \Magento\Filesystem $filesystem,
        \Magento\View\Service $viewService,
        \Magento\View\FileSystem $viewFileSystem,
        $filename = self::CONFIG_FILE_NAME
    ) {
        $this->moduleReader = $moduleReader;
        $this->rootDirectory = $filesystem->getDirectoryRead(DirectoryList::ROOT);
        $this->viewService = $viewService;
        $this->viewFileSystem = $viewFileSystem;
        $this->filename = $filename;
    }

    /**
     * Render view config object for current package and theme
     *
     * @param array $params
     * @return \Magento\Config\View
     */
    public function getViewConfig(array $params = array())
    {
        $this->viewService->updateDesignParams($params);
        /** @var $currentTheme \Magento\View\Design\ThemeInterface */
        $currentTheme = $params['themeModel'];
        $key = $currentTheme->getId();
        if (isset($this->viewConfigs[$key])) {
            return $this->viewConfigs[$key];
        }

        $configFiles = $this->moduleReader->getConfigurationFiles($this->filename);
        $themeConfigFile = $currentTheme->getCustomization()->getCustomViewConfigPath();
        if (empty($themeConfigFile) || !$this->rootDirectory->isExist($themeConfigFile)) {
            $themeConfigFile = $this->viewFileSystem->getFilename(
                $this->filename, $params
            );
        }
        if ($themeConfigFile && $this->rootDirectory->isExist($themeConfigFile)) {
            $configFiles[] = $themeConfigFile;
        }
        $config = new \Magento\Config\View($configFiles);

        $this->viewConfigs[$key] = $config;
        return $config;
    }
}
