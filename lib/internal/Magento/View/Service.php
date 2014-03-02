<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View;

use Magento\View\Design\ThemeInterface;
use Magento\UrlInterface;

/**
 * A repository for view assets
 *
 * @see \Magento\View\Asset\AssetInterface
 */
class Service implements Asset\SourceFileInterface, Asset\PublishInterface
{
    /**
     * A suffix for temporary materialization directory where pre-processed files will be written (if necessary)
     */
    const TMP_MATERIALIZATION_DIR = 'view_preprocessed';

    /**#@+
     * Public directories prefix group
     */
    const PUBLIC_VIEW_DIR   = '_view';
    const PUBLIC_THEME_DIR  = '_theme';
    /**#@-*/

    /**
     * @var \Magento\App\State
     */
    protected $appState;

    /**
     * @var \Magento\View\DesignInterface
     */
    private $design;

    /**
     * @var \Magento\View\Design\Theme\FlyweightFactory
     */
    protected $themeFactory;

    /**
     * @var \Magento\App\Filesystem
     */
    protected $filesystem;

    /**
     * @var \Magento\UrlInterface
     */
    protected $baseUrl;

    /**
     * @var \Magento\View\Asset\PreProcessor\Factory
     */
    private $preprocessorFactory;

    /**
     * @var \Magento\View\Design\FileResolution\StrategyPool
     */
    protected $resolutionPool;

    /**
     * @param \Magento\App\State $appState
     * @param \Magento\View\DesignInterface $design
     * @param \Magento\View\Design\Theme\FlyweightFactory $themeFactory
     * @param \Magento\App\Filesystem $filesystem
     * @param \Magento\UrlInterface $baseUrl
     * @param \Magento\View\Asset\PreProcessor\Factory $preprocessorFactory
     * @param \Magento\View\Design\FileResolution\StrategyPool $resolutionPool
     */
    public function __construct(
        \Magento\App\State $appState,
        \Magento\View\DesignInterface $design,
        \Magento\View\Design\Theme\FlyweightFactory $themeFactory,
        \Magento\App\Filesystem $filesystem,
        \Magento\UrlInterface $baseUrl,
        Asset\PreProcessor\Factory $preprocessorFactory,
        \Magento\View\Design\FileResolution\StrategyPool $resolutionPool
    ) {
        $this->appState = $appState;
        $this->design = $design;
        $this->filesystem = $filesystem;
        $this->themeFactory = $themeFactory;
        $this->baseUrl = $baseUrl;
        $this->preprocessorFactory = $preprocessorFactory;
        $this->resolutionPool = $resolutionPool;
    }

    /**
     * Identify file scope if it defined in file name and override 'module' parameter in $params array
     *
     * It accepts $fileId e.g. \Magento\Core::prototype/magento.css and splits it to module part and path part.
     * Then sets module path to $params['module'] and returns path part.
     *
     * @param string $fileId
     * @param array &$params
     * @return string
     * @throws \Magento\Exception
     */
    public function extractScope($fileId, array &$params)
    {
        list($module, $file) = Asset\FileId::extractModule($fileId);
        if (!empty($module)) {
            $params['module'] = $module;
        }
        return $file;
    }

    /**
     * Whether it is allowed to publish view assets
     *
     * @return bool
     */
    public function isPublishingAllowed()
    {
        return $this->appState->getMode() != \Magento\App\State::MODE_PRODUCTION;
    }

    /**
     * Whether it is prohibited publishing view assets
     *
     * @return bool
     */
    public function isPublishingDisallowed()
    {
        return $this->appState->getMode() === \Magento\App\State::MODE_DEVELOPER;
    }

    /**
     * Update required parameters with default values if custom not specified
     *
     * @param array &$params
     * @throws \UnexpectedValueException
     * @return $this
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function updateDesignParams(array &$params)
    {
        $defaults = $this->design->getDesignParams();

        // Set area
        if (empty($params['area'])) {
            $params['area'] = $defaults['area'];
        }

        // Set themeModel
        $theme = null;
        $area = $params['area'];
        if (!empty($params['themeId'])) {
            $theme = $params['themeId'];
        } elseif (isset($params['theme'])) {
            $theme = $params['theme'];
        } elseif (empty($params['themeModel']) && $area !== $defaults['area']) {
            $theme = $this->design->getConfigurationDesignTheme($area);
        }

        if ($theme) {
            $params['themeModel'] = $this->themeFactory->create($theme, $area);
            if (!$params['themeModel']) {
                throw new \UnexpectedValueException("Could not find theme '$theme' for area '$area'");
            }
        } elseif (empty($params['themeModel'])) {
            $params['themeModel'] = $defaults['themeModel'];
        }


        // Set module
        if (!array_key_exists('module', $params)) {
            $params['module'] = false;
        }

        // Set locale
        if (empty($params['locale'])) {
            $params['locale'] = $defaults['locale'];
        }
        return $this;
    }

    /**
     * Create instance of a local asset
     *
     * The asset is merely a value object that doesn't know whether the resources it refers to actually exists or not.
     * The asset object is immutable by design.
     *
     * @param string $fileId
     * @param array $params
     * @return Asset\FileId
     */
    public function createAsset($fileId, array $params = array())
    {
        $isSecure = isset($params['_secure']) ? (bool) $params['_secure'] : null;
        $this->updateDesignParams($params);
        $fileId = $this->extractScope($fileId, $params);
        if ($params['module']) {
            $fileId = $params['module'] . Asset\FileId::FILE_ID_SEPARATOR . $fileId;
        }
        return new Asset\FileId(
            $this,
            $fileId,
            $this->baseUrl->getBaseUrl(array('_type' => UrlInterface::URL_TYPE_STATIC, '_secure' => $isSecure)),
            $params['area'],
            self::resolveThemeIntoPath($params['themeModel']),
            $params['locale']
        );
    }

    /**
     * @inheritdoc
     */
    public function getSourceFile(Asset\LocalInterface $asset)
    {
        $cacheId = $asset->getRelativePath();
        $cacheHit = false;
        // TODO implement caching
        $file = false;
        if (!$cacheHit) {
            $file = $this->resolveAssetSource($asset);
            // TODO add result to cache (it can be false as well)
        }
        return $file;
    }

    /**
     * Determine the original source file for an asset
     *
     * The original source file is always different from what asset "claims" or it may not even exist.
     * This method will either locate the original file and process (materialize) it if necessary.
     * Materialization will occur only if result of preprocessing is different from the originally located file.
     *
     * @param Asset\FileId $asset
     * @return bool|string
     * @throws \LogicException
     */
    private function resolveAssetSource(Asset\FileId $asset)
    {
        $themeModel = $this->themeFactory->create($asset->getThemePath(), $asset->getAreaCode());
        /**
         * Bypass proxy, since caching is out of scope of this method intentionally
         * @var Design\FileResolution\Strategy\Fallback $fallback
         */
        $fallback = $this->resolutionPool->getViewStrategy(true);
        $file = $fallback->getViewFile(
            $asset->getAreaCode(),
            $themeModel,
            $asset->getLocaleCode(),
            $asset->getFilePath(),
            $asset->getModule()
        );
        if ($file) {
            $origContent = file_get_contents($file);
            $origContentType = pathinfo($file, PATHINFO_EXTENSION);
            $content = $origContent;
            $contentType = $origContentType;
            foreach ($this->preprocessorFactory->getPreProcessors($origContentType) as $processor) {
                list($content, $contentType) = $processor->process($content, $contentType, $asset);
            }
            if ($contentType !== $asset->getContentType()) {
                // impose an integrity check to avoid generating mismatching content type
                throw new \LogicException(
                    "The requested asset type was '{$asset->getContentType()}', but ended up with '{$contentType}'"
                );
            }
            if ($origContent != $content || $origContentType != $contentType) {
                $relPath = self::TMP_MATERIALIZATION_DIR . '/' . $asset->getRelativePath();
                $file = $this->filesystem->getPath(\Magento\App\Filesystem::VAR_DIR) . $relPath;
                $this->filesystem->getDirectoryWrite(\Magento\App\Filesystem::VAR_DIR)->writeFile($relPath, $content);
            }
        }
        return $file;
    }

    /**
     * Create a file asset value object
     *
     * $filePath is an invariant path of the file relative to directory or a base URL
     * $sourcePath is an absolute path in file system where its contents may be read
     *
     * @param string $filePath
     * @param string $sourcePath
     * @param string|null $baseUrl
     * @return Asset\File
     */
    public function createFileAsset($filePath, $sourcePath, $baseUrl = null)
    {
        if (null === $baseUrl) {
            $baseUrl = $this->baseUrl->getBaseUrl(array('_type' => UrlInterface::URL_TYPE_STATIC));
        }
        return new Asset\File($filePath, $sourcePath, $baseUrl);
    }

    /**
     * Create a remote asset value object
     *
     * @param string $url
     * @param string $contentType
     * @return Asset\Remote
     */
    public function createRemoteAsset($url, $contentType)
    {
        return new Asset\Remote($url, $contentType);
    }

    /**
     * Getter for static view file URL
     *
     * @param string $fileId
     * @return string
     */
    public function getAssetUrl($fileId)
    {
        $asset = $this->createAsset($fileId);
        return $asset->getUrl();
    }

    /**
     * A getter for static view file URL with special parameters
     *
     * To omit parameters and have them automatically determined from application state, use getAssetUrl()
     *
     * @param string $fileId
     * @param array $params
     * @return string
     * @see getAssetUrl()
     */
    public function getAssetUrlWithParams($fileId, array $params)
    {
        $asset = $this->createAsset($fileId, $params);
        return $asset->getUrl();
    }

    /**
     * @inheritdoc
     */
    public function publish(Asset\LocalInterface $asset)
    {
        if ($this->appState->getMode() === \Magento\App\State::MODE_DEVELOPER) {
            return false;
        }
        $dir = $this->filesystem->getDirectoryRead(\Magento\App\Filesystem::STATIC_VIEW_DIR);
        if ($dir->isExist($asset->getRelativePath())) {
            return true;
        }
        return $this->publishAsset($asset);
    }

    /**
     * Publish the asset
     *
     * @param Asset\LocalInterface $asset
     * @return bool
     */
    private function publishAsset(Asset\LocalInterface $asset)
    {
        $dir = $this->filesystem->getDirectoryWrite(\Magento\App\Filesystem::STATIC_VIEW_DIR);
        $rootDir = $this->filesystem->getDirectoryWrite(\Magento\App\Filesystem::ROOT_DIR);
        $source = $rootDir->getRelativePath($asset->getSourceFile());
        $destination = $asset->getRelativePath();
        return $rootDir->copyFile($source, $destination, $dir);
    }

    /**
     * Build a fully qualified path to view file using theme object and other components
     *
     * @param string $filePath
     * @param string $areaCode
     * @param ThemeInterface $theme
     * @param string $localeCode
     * @param string $module
     * @return string
     */
    public static function getAssetRelativePath($filePath, $areaCode, ThemeInterface $theme, $localeCode, $module = '')
    {
        $themePath = self::resolveThemeIntoPath($theme);
        return Asset\FileId::buildRelativePath($filePath, $areaCode, $themePath, $localeCode, $module);
    }

    /**
     * A subroutine for converting a theme model to a path fragment that can be used in URL
     *
     * @param ThemeInterface $theme
     * @return string
     */
    private static function resolveThemeIntoPath(ThemeInterface $theme)
    {
        $result = $theme->getThemePath();
        if (!$result) {
            $themeId = $theme->getId();
            if ($themeId) {
                $result = self::PUBLIC_THEME_DIR . $themeId;
            } else {
                $result = self::PUBLIC_VIEW_DIR;
            }
        }
        return $result;
    }
}
