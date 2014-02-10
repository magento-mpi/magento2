<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Css\PreProcessor;

use \Magento\View\Asset\PreProcessor\PreProcessorInterface;

/**
 * Css pre-processor less
 */
class Less implements PreProcessorInterface
{
    /**#@+
     * Temporary directories prefix group
     */
    const TMP_VIEW_DIR   = 'view';
    const TMP_THEME_DIR  = 'theme_';
    /**#@-*/

    /**
     * @var \Magento\View\FileSystem
     */
    protected $viewFileSystem;

    /**
     * @var \Magento\Less\PreProcessor
     */
    protected $lessPreProcessor;

    /**
     * @var \Magento\Css\PreProcessor\AdapterInterface
     */
    protected $adapter;

    /**
     * @var \Magento\Logger
     */
    protected $logger;

    /**
     * @param \Magento\View\FileSystem $viewFileSystem
     * @param \Magento\Less\PreProcessor $lessPreProcessor
     * @param AdapterInterface $adapter
     * @param \Magento\Logger $logger
     */
    public function __construct(
        \Magento\View\FileSystem $viewFileSystem,
        \Magento\Less\PreProcessor $lessPreProcessor,
        \Magento\Css\PreProcessor\AdapterInterface $adapter,
        \Magento\Logger $logger
    ) {
        $this->viewFileSystem = $viewFileSystem;
        $this->lessPreProcessor = $lessPreProcessor;
        $this->adapter = $adapter;
        $this->logger = $logger;
    }

    /**
     * Process LESS file content
     *
     * @param string $filePath
     * @param array $params
     * @param \Magento\Filesystem\Directory\WriteInterface $targetDirectory
     * @param null|string $sourcePath
     * @return string
     */
    public function process($filePath, $params, $targetDirectory, $sourcePath = null)
    {
        // if css file has being already found_by_fallback or prepared_by_previous_pre-processor
        if ($sourcePath) {
            return $sourcePath;
        }

        $lessFilePath = $this->replaceExtension($filePath, 'css', 'less');
        try {
            $preparedLessFileSourcePath = $this->lessPreProcessor->processLessInstructions($lessFilePath, $params);
        } catch (\Magento\Filesystem\FilesystemException $e) {
            $this->logger->logException($e);
            return $sourcePath;     // It's actually 'null'
        }

        try {
            $cssContent = $this->adapter->process($preparedLessFileSourcePath);
        } catch (\Magento\Css\PreProcessor\Adapter\AdapterException $e) {
            $this->logger->logException($e);
            return $sourcePath;     // It's actually 'null'
        }

        $tmpFilePath = $this->buildTmpFilePath($filePath, $params);

        $targetDirectory->writeFile($tmpFilePath, $cssContent);
        return $targetDirectory->getAbsolutePath($tmpFilePath);
    }

    /**
     * Build unique file path for a view file that includes area/theme/locale/module parts
     *
     * @param string $file
     * @param array $params - 'themeModel', 'area', 'locale', 'module' keys are used
     * @return string
     */
    protected function buildTmpFilePath($file, array $params)
    {
        /** @var $theme \Magento\View\Design\ThemeInterface */
        $theme = $params['themeModel'];
        $designPath = null;
        if ($theme->getThemePath()) {
            $designPath = $theme->getThemePath();
        } elseif ($theme->getId()) {
            $designPath = self::TMP_THEME_DIR . $theme->getId();
        }

        $parts = array();
        $parts[] = self::TMP_VIEW_DIR;
        $parts[] = $params['area'];
        if ($designPath) {
            $parts[] = $designPath;
        }
        $parts[] = $params['locale'];
        if ($params['module']) {
            $parts[] = $params['module'];
        }
        $parts[] = $file;

        $publicFile = join('/', $parts);

        return $publicFile;
    }

    /**
     * @param string $filePath
     * @param string $search
     * @param string $replace
     * @return string
     */
    protected function replaceExtension($filePath, $search, $replace)
    {
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        if ($extension == $search) {
            $dotPosition = strrpos($filePath, '.');
            $filePath = substr($filePath, 0, $dotPosition + 1) . $replace;
        }

        return $filePath;
    }
}
