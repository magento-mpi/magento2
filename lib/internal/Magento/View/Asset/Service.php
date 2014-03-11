<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Asset;

use \Magento\UrlInterface;

class Service
{
    /**
     * @var \Magento\UrlInterface
     */
    private $baseUrl;

    /**
     * @var \Magento\View\Asset\PathGenerator
     */
    private $pathGenerator;

    /**
     * @var \Magento\View\DesignInterface
     */
    private $design;

    /**
     * @var \Magento\View\Design\Theme\Provider
     */
    private $themeProvider;

    /**
     * @var \Magento\View\Service
     */
    private $viewService;

    /**
     * @param \Magento\UrlInterface $baseUrl
     * @param PathGenerator $pathGenerator
     * @param \Magento\View\DesignInterface $design
     * @param \Magento\View\Design\Theme\Provider $themeProvider
     * @param \Magento\View\Service $viewService
     */
    public function __construct(
        \Magento\UrlInterface $baseUrl,
        PathGenerator $pathGenerator,
        \Magento\View\DesignInterface $design,
        \Magento\View\Design\Theme\Provider $themeProvider,
        \Magento\View\Service $viewService
    ) {
        $this->baseUrl = $baseUrl;
        $this->pathGenerator = $pathGenerator;
        $this->design = $design;
        $this->themeProvider = $themeProvider;
        $this->viewService = $viewService;
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
        list($module, $file) = FileId::extractModule($fileId);
        if (!empty($module)) {
            $params['module'] = $module;
        }
        return $file;
    }

    /**
     * Create instance of a local asset
     *
     * The asset is merely a value object that doesn't know whether the resources it refers to actually exist or not.
     * The asset object is immutable by design.
     *
     * @param string $fileId
     * @param array $params
     * @return FileId
     */
    public function createAsset($fileId, array $params = array())
    {
        $this->updateDesignParams($params);
        $fileId = $this->extractScope($fileId, $params);
        $isSecure = isset($params['_secure']) ? (bool) $params['_secure'] : null;
        if ($params['module']) {
            $fileId = $params['module'] . FileId::FILE_ID_SEPARATOR . $fileId;
        }
        return new FileId(
            $this->pathGenerator,
            $this->viewService,
            $fileId,
            $this->baseUrl->getBaseUrl(array('_type' => UrlInterface::URL_TYPE_STATIC, '_secure' => $isSecure)),
            $params['area'],
            $this->pathGenerator->getThemePath($params['themeModel']),
            $params['locale']
        );
    }

    /**
     * Create a file asset value object
     *
     * @param string $filePath Invariant path of the file relative to directory or a base URL
     * @param string $sourcePath Absolute path in file system where its contents may be read
     * @param string|null $baseUrl
     * @return File
     */
    public function createFileAsset($filePath, $sourcePath, $baseUrl = null)
    {
        if (null === $baseUrl) {
            $baseUrl = $this->baseUrl->getBaseUrl(array('_type' => UrlInterface::URL_TYPE_STATIC));
        }
        return new File($filePath, $sourcePath, $baseUrl);
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
}
