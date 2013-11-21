<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View;

/**
 * Builds URLs for publicly accessible files
 */
class Url
{
    /**
     * XPath for configuration setting of signing static files
     */
    const XML_PATH_STATIC_FILE_SIGNATURE = 'dev/static/sign';

    /**
     * @var \Magento\Filesystem
     */
    protected $_filesystem;

    /**
     * @var \Magento\App\Dir
     */
    protected $_dirs;

    /**
     * @var \Magento\View\Service
     */
    protected $_viewService;

    /**
     * @var \Magento\View\Publisher
     */
    protected $_publisher;

    /**
     * @var \Magento\View\DeployedFilesManager
     */
    protected $_deployedFileManager;

    /**
     * @var \Magento\UrlInterface
     */
    protected $_urlBuilder;

    /**
     * @var \Magento\View\Url\ConfigInterface
     */
    protected $_config;

    /**
     * Map urls to app dirs
     *
     * @var array
     */
    protected $_fileUrlMap;

    /**
     * @param \Magento\Filesystem $filesystem
     * @param \Magento\App\Dir $dirs
     * @param \Magento\UrlInterface $urlBuilder
     * @param Url\ConfigInterface $config
     * @param Service $viewService
     * @param Publisher $publisher
     * @param DeployedFilesManager $deployedFileManager
     * @param array $fileUrlMap
     */
    public function __construct(
        \Magento\Filesystem $filesystem,
        \Magento\App\Dir $dirs,
        \Magento\UrlInterface $urlBuilder,
        \Magento\View\Url\ConfigInterface $config,
        \Magento\View\Service $viewService,
        \Magento\View\Publisher $publisher,
        \Magento\View\DeployedFilesManager $deployedFileManager,
        array $fileUrlMap = array()
    ) {
        $this->_filesystem = $filesystem;
        $this->_dirs = $dirs;
        $this->_urlBuilder = $urlBuilder;
        $this->_config = $config;
        $this->_viewService = $viewService;
        $this->_publisher = $publisher;
        $this->_deployedFileManager = $deployedFileManager;
        $this->_fileUrlMap = $fileUrlMap;
    }

    /**
     * Retrieve view file URL
     *
     * Get URL to file base on theme file identifier.
     * Publishes file there, if needed.
     *
     * @param string $fileId
     * @param array $params
     * @return string
     */
    public function getViewFileUrl($fileId, array $params = array())
    {
        $isSecure = isset($params['_secure']) ? (bool) $params['_secure'] : null;
        unset($params['_secure']);

        $publicFilePath = $this->getViewFilePublicPath($fileId, $params);
        $url = $this->getPublicFileUrl($publicFilePath, $isSecure);

        return $url;
    }

    /**
     * Get public file path
     *
     * @param string $fileId
     * @param array $params
     * @return string
     */
    public function getViewFilePublicPath($fileId, array $params = array())
    {
        $this->_viewService->updateDesignParams($params);
        $filePath = $this->_viewService->extractScope($fileId, $params);

        $publicFilePath = $this->_getFilesManager()->getPublicFilePath($filePath, $params);

        return $publicFilePath;
    }

    /**
     * Get url to public file
     *
     * @param string $publicFilePath
     * @param bool|null $isSecure
     * @return string
     * @throws \Magento\Exception
     */
    public function getPublicFileUrl($publicFilePath, $isSecure = null)
    {
        foreach ($this->_fileUrlMap as $urlMap) {
            $dir = $this->_dirs->getDir($urlMap['value']);
            if (strpos($publicFilePath, $dir) === 0) {
                $relativePath = ltrim(substr($publicFilePath, strlen($dir)), '\\/');
                $relativePath = str_replace(DIRECTORY_SEPARATOR, '/', $relativePath);
                $url = $this->_urlBuilder->getBaseUrl(
                    array(
                        '_type' => $urlMap['key'],
                        '_secure' => $isSecure
                    )
                ) . $relativePath;

                if ($this->_isStaticFilesSigned() && $this->_viewService->isViewFileOperationAllowed()) {
                    $fileMTime = $this->_filesystem->getMTime($publicFilePath);
                    $url .= '?' . $fileMTime;
                }
                return $url;
            }
        }
        throw new \Magento\Exception(
            "Cannot build URL for the file '$publicFilePath' because it does not reside in a public directory."
        );
    }

    /**
     * Check if static files have to be signed
     *
     * @return bool
     */
    protected function _isStaticFilesSigned()
    {
        return (bool)$this->_config->getValue(self::XML_PATH_STATIC_FILE_SIGNATURE);
    }

    /**
     * Get files manager that is able to return file public path
     *
     * @return \Magento\View\PublicFilesManagerInterface
     */
    protected function _getFilesManager()
    {
        if ($this->_viewService->isViewFileOperationAllowed()) {
            $filesManager = $this->_publisher;
        } else {
            $filesManager = $this->_deployedFileManager;
        }

        return $filesManager;
    }
}
