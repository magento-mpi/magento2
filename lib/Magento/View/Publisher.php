<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View;

use Magento\Filesystem\Directory\WriteInterface;

class Publisher implements \Magento\View\PublicFilesManagerInterface
{
    /**#@+
     * Extensions group for static files
     */
    const CONTENT_TYPE_CSS = 'css';
    const CONTENT_TYPE_JS  = 'js';
    /**#@-*/

    /**#@+
     * Protected extensions group for publication mechanism
     */
    const CONTENT_TYPE_PHP   = 'php';
    const CONTENT_TYPE_PHTML = 'phtml';
    const CONTENT_TYPE_XML   = 'xml';
    /**#@-*/

    /**#@+
     * Public directories prefix group
     */
    const PUBLIC_MODULE_DIR = '_module';
    const PUBLIC_VIEW_DIR   = '_view';
    const PUBLIC_THEME_DIR  = '_theme';
    /**#@-*/

    /**
     * @var \Magento\App\Filesystem
     */
    protected $_filesystem;

    /**
     * Helper to process css content
     *
     * @var \Magento\View\Url\CssResolver
     */
    protected $_cssUrlResolver;

    /**
     * @var \Magento\View\Service
     */
    protected $_viewService;

    /**
     * @var \Magento\View\FileSystem
     */
    protected $_viewFileSystem;

    /**
     * @var \Magento\Logger
     */
    protected $_logger;

    /**
     * Indicates how to materialize view files: with or without "duplication"
     *
     * @var bool
     */
    protected $_allowDuplication;

    /**
     * @var \Magento\Module\Dir\Reader
     */
    protected $_modulesReader;

    /**
     * @var WriteInterface
     */
    protected $rootDirectory;

    /**
     * @var \Magento\View\Asset\PreProcessor\PreProcessorInterface
     */
    protected $preProcessor;

    /**
     * @param \Magento\Logger $logger
     * @param \Magento\App\Filesystem $filesystem
     * @param \Magento\View\Url\CssResolver $cssUrlResolver
     * @param Service $viewService
     * @param FileSystem $viewFileSystem
     * @param \Magento\Module\Dir\Reader $modulesReader
     * @param \Magento\View\Asset\PreProcessor\PreProcessorInterface $preProcessor
     * @param bool $allowDuplication
     */
    public function __construct(
        \Magento\Logger $logger,
        \Magento\App\Filesystem $filesystem,
        \Magento\View\Url\CssResolver $cssUrlResolver,
        \Magento\View\Service $viewService,
        \Magento\View\FileSystem $viewFileSystem,
        \Magento\Module\Dir\Reader $modulesReader,
        \Magento\View\Asset\PreProcessor\PreProcessorInterface $preProcessor,
        $allowDuplication
    ) {
        $this->_filesystem = $filesystem;
        $this->rootDirectory = $filesystem->getDirectoryWrite(\Magento\App\Filesystem::ROOT_DIR);
        $this->_cssUrlResolver = $cssUrlResolver;
        $this->_viewService = $viewService;
        $this->_viewFileSystem = $viewFileSystem;
        $this->_modulesReader = $modulesReader;
        $this->_logger = $logger;
        $this->_allowDuplication = $allowDuplication;
        $this->preProcessor = $preProcessor;
    }

    /**
     * Get published file path
     *
     * @param  string $filePath
     * @param  array $params
     * @return string
     */
    public function getPublicFilePath($filePath, $params)
    {
        return $this->_getPublishedFilePath($filePath, $params);
    }

    /**
     * Publish file identified by $fileId basing on information about parent file path and name.
     *
     * @param string $fileId URL to the file that was extracted from $parentFilePath
     * @param string $parentFilePath path to the file
     * @param string $parentFileName original file name identifier that was requested for processing
     * @param array $params theme/module parameters array
     * @return string
     */
    protected function _publishRelatedViewFile($fileId, $parentFilePath, $parentFileName, $params)
    {
        $relativeFilePath = $this->_getRelatedViewFile($fileId, $parentFilePath, $parentFileName, $params);
        return $this->_getPublishedFilePath($relativeFilePath, $params);
    }

    /**
     * Get published file path
     *
     * Check, if requested theme file has public access, and move it to public folder, if the file has no public access
     *
     * @param  string $filePath
     * @param  array $params
     * @return string
     * @throws \Magento\Exception
     */
    protected function _getPublishedFilePath($filePath, $params)
    {
        //TODO: Do we need this? It throws exception in production mode!
        if (!$this->_viewService->isViewFileOperationAllowed()) {
            throw new \Magento\Exception('Filesystem operations are not permitted for view files');
        }

        // 1. Fallback look-up for view files. Remember it can be file of any type: CSS, LESS, JS, image
        $sourcePath = $this->_viewFileSystem->getViewFile($filePath, $params);

        // 2. If $sourcePath returned actually not exists replace it with null value.
        if (!$this->rootDirectory->isExist($this->rootDirectory->getRelativePath($sourcePath))) {
            $sourcePath = null;
        }

        // 3. Target directory to save temporary files in. It was 'pub/static' dir, but I guess it's more correct to have it in 'var/tmp' dir.
        $targetDirectory = $this->_filesystem->getDirectoryWrite(\Magento\App\Filesystem::VAR_DIR);

        // 4. Execute asset pre-processors last
        //      in case if $sourcePath was null, then pre-processors will be executed and original source file
        //          will be processed, then new $sourcePath targeting pre-processed file in 'var/tmp' dir
        //          will be returned back
        //      in case if $sourcePath was not null then $sourcePath passed will be returned back
        $sourcePath = $this->preProcessor->process($filePath, $params, $targetDirectory, $sourcePath);

        // 5. If $sourcePath returned still doesn't exists throw Exception
        if (!$this->rootDirectory->isExist($this->rootDirectory->getRelativePath($sourcePath))) {
            throw new \Magento\Exception("Unable to locate theme file '{$sourcePath}'.");
        }

        // 6.
        // If $sourcePath points to file in 'pub/lib' dir - no publishing required
        // If $sourcePath points to file with protected extension - no publishing, return unchanged
        // If $sourcePath points to file in 'pub/static' dir - no publishing required
        // If $sourcePath points to CSS file and developer mode is enabled - publish file
        if (!$this->_needToPublishFile($sourcePath)) {
            return $sourcePath;
        }

        return $this->_publishFile($filePath, $params, $sourcePath);
    }

    /**
     * Publish file
     *
     * @param string $filePath
     * @param array $params
     * @param string $sourcePath
     * @return string
     */
    protected function _publishFile($filePath, $params, $sourcePath)
    {
        //TODO: Do we really need not normalizePath() calls below?
        //$filePath = $this->_viewFileSystem->normalizePath($filePath);
        //$sourcePath = $this->_viewFileSystem->normalizePath($sourcePath);
        $targetPath = $this->_buildPublishedFilePath($filePath, $params, $sourcePath);

        /* Validate whether file needs to be published */
        $isCssFile = $this->_getExtension($filePath) == self::CONTENT_TYPE_CSS;
        if ($isCssFile) {
            $cssContent = $this->_getPublicCssContent($sourcePath, $targetPath, $filePath, $params);
        }

        $targetDirectory = $this->_filesystem->getDirectoryWrite(\Magento\App\Filesystem::STATIC_VIEW_DIR);
        $sourcePathRelative = $this->rootDirectory->getRelativePath($sourcePath);
        $targetPathRelative = $targetDirectory->getRelativePath($targetPath);

        // CSS files generated out of LESS has following logic of publication:
        //  - No sense to check 'mtime' cause $sourcePath points to file in 'var/tmp directory'
        //  - CSS content exists and should be written
        //  - No sense to write 'mtime' cause it's not have any value
        //  - Any sense to check file exists in public dir?

        // Is there a case when $targetPathRelative is a directory?

        $fileMTime = $this->rootDirectory->stat($sourcePathRelative)['mtime'];
        if (!$targetDirectory->isExist($targetPathRelative)
            || $fileMTime != $targetDirectory->stat($targetPathRelative)['mtime']) {
            if (isset($cssContent)) {
                $targetDirectory->writeFile($targetPathRelative, $cssContent);
                $targetDirectory->touch($targetPathRelative, $fileMTime);
            } elseif ($this->rootDirectory->isFile($sourcePathRelative)) {
                $this->rootDirectory->copyFile($sourcePathRelative, $targetPathRelative, $targetDirectory);
                $targetDirectory->touch($targetPathRelative, $fileMTime);
            } elseif (!$targetDirectory->isDirectory($targetPathRelative)) {
                $targetDirectory->create($targetPathRelative);
            }
        }

        //Check if data of LESS correctly written to MAP
        $this->_viewFileSystem->notifyViewFileLocationChanged($targetPath, $filePath, $params);
        return $targetPath;
    }

    /**
     * Build published file path
     *
     * @param string $filePath
     * @param array $params
     * @param string $sourcePath
     * @return string
     */
    protected function _buildPublishedFilePath($filePath, $params, $sourcePath)
    {
        $isCssFile = $this->_getExtension($filePath) == self::CONTENT_TYPE_CSS;
        if ($this->_allowDuplication || $isCssFile) {
            $targetPath = $this->_buildPublicViewRedundantFilename($filePath, $params);
        } else {
            $targetPath = $this->_buildPublicViewSufficientFilename($sourcePath, $params);
        }
        $targetPath = $this->_buildPublicViewFilename($targetPath);

        return $targetPath;
    }

    /**
     * Determine whether a file needs to be published
     *
     * Js files (actually all files located in 'pub/lib' dir) should not be published.
     * All other files must be processed either if they are not published already (located in 'pub/static'),
     * or if they are css-files and we're working in developer mode.
     *
     * @param string $filePath
     * @return bool
     */
    protected function _needToPublishFile($filePath)
    {
        $jsPath = $this->_filesystem->getPath(\Magento\App\Filesystem::PUB_LIB_DIR) . '/';
        $filePath = str_replace('\\', '/', $filePath);
        if (strncmp($filePath, $jsPath, strlen($jsPath)) === 0) {
            return false;
        }

        $protectedExtensions = array(
            self::CONTENT_TYPE_PHP,
            self::CONTENT_TYPE_PHTML,
            self::CONTENT_TYPE_XML
        );
        if (in_array($this->_getExtension($filePath), $protectedExtensions)) {
            return false;
        }

        $themePath = $this->_filesystem->getPath(\Magento\App\Filesystem::STATIC_VIEW_DIR) . '/';
        if (strncmp($filePath, $themePath, strlen($themePath)) === 0) {
            return false;
        }

        return ($this->_viewService->getAppMode() == \Magento\App\State::MODE_DEVELOPER)
            && $this->_getExtension($filePath) == self::CONTENT_TYPE_CSS;
    }

    /**
     * Get file extension by file path
     *
     * @param string $filePath
     * @return string
     */
    protected function _getExtension($filePath)
    {
        $dotPosition = strrpos($filePath, '.');
        return strtolower(substr($filePath, $dotPosition + 1));
    }

    /**
     * Build public filename for a theme file that always includes area/package/theme/locate parameters
     *
     * @param string $file
     * @param array $params
     * @return string
     */
    protected function _buildPublicViewRedundantFilename($file, array $params)
    {
        /** @var $theme \Magento\View\Design\ThemeInterface */
        $theme = $params['themeModel'];
        if ($theme->getThemePath()) {
            $designPath = $theme->getThemePath();
        } elseif ($theme->getId()) {
            $designPath = self::PUBLIC_THEME_DIR . $theme->getId();
        } else {
            $designPath = self::PUBLIC_VIEW_DIR;
        }

        $publicFile = $params['area'] . '/' . $designPath . '/' . $params['locale'] .
            ($params['module'] ? '/' . $params['module'] : '') . '/' . $file;

        return $publicFile;
    }

    /**
     * Build public filename for a view file that sufficiently depends on the passed parameters
     *
     * @param string $filename
     * @param array $params
     * @return string
     */
    protected function _buildPublicViewSufficientFilename($filename, array $params)
    {
        $designDir = $this->_filesystem->getPath(\Magento\App\Filesystem::THEMES_DIR) . '/';
        if (0 === strpos($filename, $designDir)) {
            // theme file
            $publicFile = substr($filename, strlen($designDir));
        } else {
            // modular file
            $module = $params['module'];
            $moduleDir = $this->_modulesReader->getModuleDir('theme', $module) . '/';
            $publicFile = substr($filename, strlen($moduleDir));
            $publicFile = self::PUBLIC_MODULE_DIR . '/' . $module . '/' . $publicFile;
        }
        return $publicFile;
    }

    /**
     * Retrieve processed CSS file content that contains URLs relative to the specified public directory
     *
     * @param string $sourcePath Absolute path to the current location of CSS file
     * @param string $publicPath Absolute path to location of the CSS file, where it will be published
     * @param string $fileName File name used for reference
     * @param array $params Design parameters
     * @return string
     */
    protected function _getPublicCssContent($sourcePath, $publicPath, $fileName, $params)
    {
        $content = $this->rootDirectory->readFile($this->rootDirectory->getRelativePath($sourcePath));

        $callback = function ($fileId, $originalPath) use ($fileName, $params) {
            $relatedPathPublic = $this->_publishRelatedViewFile(
                $fileId, $originalPath, $fileName, $params
            );
            return $relatedPathPublic;
        };
        try {
            $content = $this->_cssUrlResolver->replaceCssRelativeUrls(
                $content,
                $this->_viewFileSystem->normalizePath($sourcePath),
                $this->_viewFileSystem->normalizePath($publicPath),
                $callback
            );
        } catch (\Magento\Exception $e) {
            $this->_logger->logException($e);
        }
        return $content;
    }

    /**
     * Build path to file located in public folder
     *
     * @param string $file
     * @return string
     */
    protected function _buildPublicViewFilename($file)
    {
        return $this->_viewService->getPublicDir() . '/' . $file;
    }

    /**
     * Get relative $fileUrl based on information about parent file path and name.
     *
     * @param string $fileId URL to the file that was extracted from $parentFilePath
     * @param string $parentFilePath path to the file
     * @param string $parentFileName original file name identifier that was requested for processing
     * @param array $params theme/module parameters array
     * @return string
     */
    protected function _getRelatedViewFile($fileId, $parentFilePath, $parentFileName, &$params)
    {
        if (strpos($fileId, \Magento\View\Service::SCOPE_SEPARATOR)) {
            $filePath = $this->_viewService->extractScope($this->_viewFileSystem->normalizePath($fileId), $params);
        } else {
            /* Check if module file overridden on theme level based on _module property and file path */
            $themesPath = $this->_filesystem->getPath(\Magento\App\Filesystem::THEMES_DIR);
            if ($params['module'] && strpos($parentFilePath, $themesPath) === 0) {
                /* Add module directory to relative URL */
                $filePath = dirname($params['module'] . '/' . $parentFileName)
                    . '/' . $fileId;
                if (strpos($filePath, $params['module']) === 0) {
                    $filePath = ltrim(str_replace($params['module'], '', $filePath), '/');
                } else {
                    $params['module'] = false;
                }
            } else {
                $filePath = dirname($parentFileName) . '/' . $fileId;
            }
        }

        return $filePath;
    }
}
