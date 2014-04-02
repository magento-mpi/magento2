<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Asset;

use \Magento\UrlInterface;

/**
 * A repository service for view assets
 */
class Repository
{
    /**
     * @var \Magento\UrlInterface
     */
    private $baseUrl;

    /**
     * @var \Magento\View\DesignInterface
     */
    private $design;

    /**
     * @var \Magento\View\Design\Theme\Provider
     */
    private $themeProvider;

    /**
     * @var \Magento\View\Asset\File\Source
     */
    private $assetSource;

    /**
     * @var string
     */
    private $appMode;

    /**
     * @param \Magento\UrlInterface $baseUrl
     * @param \Magento\View\DesignInterface $design
     * @param \Magento\View\Design\Theme\Provider $themeProvider
     * @param \Magento\View\Asset\File\Source $assetSource
     * @param string $appMode
     */
    public function __construct(
        \Magento\UrlInterface $baseUrl,
        \Magento\View\DesignInterface $design,
        \Magento\View\Design\Theme\Provider $themeProvider,
        \Magento\View\Asset\File\Source $assetSource,
        $appMode = \Magento\App\State::MODE_DEFAULT
    ) {
        $this->baseUrl = $baseUrl;
        $this->design = $design;
        $this->themeProvider = $themeProvider;
        $this->assetSource = $assetSource;
        $this->appMode = $appMode;
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
            $params['themeModel'] = $this->themeProvider->getThemeModel($theme, $area);
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
        list($module, $file) = File::extractModule($fileId);
        if (!empty($module)) {
            $params['module'] = $module;
        }
        return $file;
    }

    /**
     * Create a file asset that's subject of fallback system
     *
     * @param string $fileId
     * @param array $params
     * @return File
     */
    public function createAsset($fileId, array $params = array())
    {
        $this->updateDesignParams($params);
        $filePath = $this->extractScope($fileId, $params);
        $isSecure = isset($params['_secure']) ? (bool) $params['_secure'] : null;
        if ($params['module']) {
            $fileId = $params['module'] . File::FILE_ID_SEPARATOR . $filePath;
        }
        $themePath = $this->design->getThemePath($params['themeModel']);
        if ($this->appMode == \Magento\App\State::MODE_PRODUCTION) {
            $locale = ''; // a workaround while support for locale is not implemented in production mode
        } else {
            $locale = $params['locale'];
        }
        $context = new File\FallbackContext(
            $this->baseUrl->getBaseUrl(array('_type' => UrlInterface::URL_TYPE_STATIC, '_secure' => $isSecure)),
            $params['area'],
            $themePath,
            $locale
        );
        return new File($this->assetSource, $context, $fileId, $this->inferContentType($filePath));
    }

    /**
     * Create a file asset similar to an existing local asset by using its context
     *
     * @param string $fileId
     * @param LocalInterface $similarTo
     * @return File
     */
    public function createSimilar($fileId, LocalInterface $similarTo)
    {
        return new File($this->assetSource, $similarTo->getContext(), $fileId, $this->inferContentType($fileId));
    }

    /**
     * Create a file asset with an arbitrary path
     *
     * This kind of file is not subject of fallback system
     * Client code is responsible for ensuring that the file is in specified directory
     *
     * @param string $filePath
     * @param string $dirPath
     * @param string $baseDirType
     * @param string $baseUrlType
     * @return File
     */
    public function createArbitrary(
        $filePath,
        $dirPath,
        $baseDirType = \Magento\App\Filesystem::STATIC_VIEW_DIR,
        $baseUrlType = UrlInterface::URL_TYPE_STATIC
    ) {
        $baseUrl = $this->baseUrl->getBaseUrl(array('_type' => $baseUrlType));
        $context = new File\Context($baseUrl, $baseDirType, $dirPath);
        $contentType = $this->inferContentType($filePath);
        return new File($this->assetSource, $context, $filePath, $contentType);
    }

    /**
     * Create a file asset with path relative to specified local asset
     *
     * @param string $fileId
     * @param LocalInterface $relativeTo
     * @return File
     */
    public function createRelative($fileId, LocalInterface $relativeTo)
    {
        list($module, $filePath) = File::extractModule($fileId);
        if ($module) {
            return $this->createSimilar($fileId, $relativeTo);
        }
        $newFileId = \Magento\View\FileSystem::normalizePath(dirname($relativeTo->getFilePath()) . '/' . $filePath);
        return $this->createSimilar($newFileId, $relativeTo);
    }

    /**
     * Attempt to detect content type of an asset by evaluating extension of file path
     *
     * @param string $path
     * @return string
     */
    private function inferContentType($path)
    {
        return pathinfo($path, PATHINFO_EXTENSION);
    }

    /**
     * Create a remote asset value object
     *
     * @param string $url
     * @param string $contentType
     * @return Remote
     */
    public function createRemoteAsset($url, $contentType)
    {
        return new Remote($url, $contentType);
    }

    /**
     * Getter for static view file URL
     *
     * @param string $fileId
     * @return string
     */
    public function getUrl($fileId)
    {
        $asset = $this->createAsset($fileId);
        return $asset->getUrl();
    }

    /**
     * A getter for static view file URL with special parameters
     *
     * To omit parameters and have them automatically determined from application state, use getUrl()
     *
     * @param string $fileId
     * @param array $params
     * @return string
     * @see getUrl()
     */
    public function getUrlWithParams($fileId, array $params)
    {
        $asset = $this->createAsset($fileId, $params);
        return $asset->getUrl();
    }
}
