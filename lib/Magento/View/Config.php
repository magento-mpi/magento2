<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\View;

use Magento\Filesystem\Directory\ReadInterface;

/**
 * Handles theme view.xml files
 */
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
     * Root directory
     *
     * @var ReadInterface
     */
    protected $rootDirectory;

    /**
     * View service
     *
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
     * File name
     *
     * @var string
     */
    protected $filename;

    /**
     * File iterator factory
     *
     * @var \Magento\Framework\Config\FileIteratorFactory
     */
    protected $fileIteratorFactory;

    /**
     * Constructor
     *
     * @param \Magento\Module\Dir\Reader $moduleReader
     * @param \Magento\Framework\App\Filesystem $filesystem
     * @param \Magento\View\Service  $viewService
     * @param \Magento\View\FileSystem $viewFileSystem
     * @param \Magento\Framework\Config\FileIteratorFactory $fileIteratorFactory
     * @param string $filename
     */
    public function __construct(
        \Magento\Module\Dir\Reader $moduleReader,
        \Magento\Framework\App\Filesystem $filesystem,
        \Magento\View\Service $viewService,
        \Magento\View\FileSystem $viewFileSystem,
        \Magento\Framework\Config\FileIteratorFactory $fileIteratorFactory,
        $filename = self::CONFIG_FILE_NAME
    ) {
        $this->moduleReader = $moduleReader;
        $this->rootDirectory = $filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem::ROOT_DIR);
        $this->viewService = $viewService;
        $this->viewFileSystem = $viewFileSystem;
        $this->filename = $filename;
        $this->fileIteratorFactory = $fileIteratorFactory;
    }

    /**
     * Render view config object for current package and theme
     *
     * @param array $params
     * @return \Magento\Framework\Config\View
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

        $configFiles = $this->moduleReader->getConfigurationFiles($this->filename)->toArray();

        $themeConfigFile = $currentTheme->getCustomization()->getCustomViewConfigPath();
        if (empty($themeConfigFile) || !$this->rootDirectory->isExist(
            $this->rootDirectory->getRelativePath($themeConfigFile)
        )
        ) {
            $themeConfigFile = $this->viewFileSystem->getFilename($this->filename, $params);
        }
        if ($themeConfigFile && $this->rootDirectory->isExist($this->rootDirectory->getRelativePath($themeConfigFile))
        ) {
            $configFiles[$this->rootDirectory->getRelativePath(
                $themeConfigFile
            )] = $this->rootDirectory->readFile(
                $this->rootDirectory->getRelativePath($themeConfigFile)
            );
        }
        $config = new \Magento\Framework\Config\View($configFiles);

        $this->viewConfigs[$key] = $config;
        return $config;
    }
}
