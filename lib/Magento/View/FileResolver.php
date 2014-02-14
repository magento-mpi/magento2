<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View;

class FileResolver
{
    /**
     * XPath for configuration setting of signing static files
     */
    const XML_PATH_STATIC_FILE_SIGNATURE = 'dev/static/sign';

    /**
     * @var \Magento\App\Filesystem
     */
    protected $_filesystem;

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
    protected $_baseUrl;

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
     * @var \Magento\View\FileSystem
     */
    protected $_viewFileSystem;

    /**
     * @param \Magento\App\Filesystem $filesystem
     * @param \Magento\UrlInterface $baseUrl
     * @param Url\ConfigInterface $config
     * @param Service $viewService
     * @param Publisher $publisher
     * @param DeployedFilesManager $deployedFileManager
     * @param \Magento\View\FileSystem $viewFileSystem,
     * @param array $fileUrlMap
     */
    public function __construct(
        \Magento\App\Filesystem $filesystem,
        \Magento\UrlInterface $baseUrl,
        \Magento\View\Url\ConfigInterface $config,
        \Magento\View\Service $viewService,
        \Magento\View\Publisher $publisher,
        \Magento\View\DeployedFilesManager $deployedFileManager,
        \Magento\View\FileSystem $viewFileSystem,
        array $fileUrlMap = array()
    ) {
        $this->_filesystem = $filesystem;
        $this->_baseUrl = $baseUrl;
        $this->_config = $config;
        $this->_viewService = $viewService;
        $this->_publisher = $publisher;
        $this->_deployedFileManager = $deployedFileManager;
        $this->_viewFileSystem = $viewFileSystem;
        $this->_fileUrlMap = $fileUrlMap;
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
        $filePath = $this->_viewService->extractScope($this->_viewFileSystem->normalizePath($fileId), $params);

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
            $dir = $this->_filesystem->getPath($urlMap['value']);
            $publicFilePath = str_replace('\\', '/', $publicFilePath);
            if (strpos($publicFilePath, $dir) === 0) {
                $relativePath = ltrim(substr($publicFilePath, strlen($dir)), '\\/');
                $url = $this->_baseUrl->getBaseUrl(
                        array(
                            '_type' => $urlMap['key'],
                            '_secure' => $isSecure
                        )
                    ) . $relativePath;

                if ($this->_isStaticFilesSigned() && $this->_viewService->isViewFileOperationAllowed()) {
                    $directory = $this->_filesystem->getDirectoryRead(\Magento\App\Filesystem::ROOT_DIR);
                    $fileMTime = $directory->stat($directory->getRelativePath($publicFilePath))['mtime'];
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
