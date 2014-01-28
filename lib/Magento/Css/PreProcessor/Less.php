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
    const TMP_LESS_DIR   = 'less';
    const TMP_ROOT_DIR   = '_view';
    const TMP_THEME_DIR  = '_theme';

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
     * @param \Magento\View\FileSystem $viewFileSystem
     * @param \Magento\Less\PreProcessor $lessPreProcessor
     * @param AdapterInterface $adapter
     */
    public function __construct(
        \Magento\View\FileSystem $viewFileSystem,
        \Magento\Less\PreProcessor $lessPreProcessor,
        \Magento\Css\PreProcessor\AdapterInterface $adapter
    ) {
        $this->viewFileSystem = $viewFileSystem;
        $this->lessPreProcessor = $lessPreProcessor;
        $this->adapter = $adapter;
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

        $lessFilePath = $this->replaceExtension($filePath, '.css', '.less');
        $preparedLessFileSourcePath = $this->lessPreProcessor->processLessInstructions($lessFilePath, $params);
        $cssContent = $this->adapter->process($preparedLessFileSourcePath);

        // doesn't matter where exact file has been found, we use original file identifier
        // see \Magento\View\Publisher::_buildPublishedFilePath() for details and make similar function
        $tmpFilePath = $this->buildTmpFilePath($filePath, $params);

        $targetDirectory->writeFile($tmpFilePath, $cssContent);
        return $targetDirectory->getAbsolutePath($tmpFilePath);
    }

    /**
     * Build public filename for a theme file that includes area/package/theme/locale parameters
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
        $parts[] = self::TMP_ROOT_DIR;
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
     * @return mixed
     */
    protected function replaceExtension($filePath, $search, $replace)
    {
        //TODO: Implement better way to replace file extension

        return $lessFilePath = str_replace($search, $replace, $filePath);
    }
}
