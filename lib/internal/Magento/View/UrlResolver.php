<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View;

/**
 * Class for resolving file resources to URLs for them
 */
class UrlResolver
{
    /**
     * XPath for configuration setting of signing static files
     */
    const XML_PATH_STATIC_FILE_SIGNATURE = 'dev/static/sign';

    /**
     * @var \Magento\App\Filesystem
     */
    protected $filesystem;

    /**
     * @var \Magento\View\Service
     */
    protected $viewService;

    /**
     * @var \Magento\UrlInterface
     */
    protected $baseUrl;

    /**
     * @var \Magento\View\Url\ConfigInterface
     */
    protected $config;

    /**
     * Map urls to app dirs
     *
     * @var array
     */
    protected $fileUrlMap;

    /**
     * @param \Magento\App\Filesystem $filesystem
     * @param \Magento\UrlInterface $baseUrl
     * @param Url\ConfigInterface $config
     * @param Service $viewService
     * @param array $fileUrlMap
     */
    public function __construct(
        \Magento\App\Filesystem $filesystem,
        \Magento\UrlInterface $baseUrl,
        Url\ConfigInterface $config,
        Service $viewService,
        array $fileUrlMap = array()
    ) {
        $this->filesystem = $filesystem;
        $this->baseUrl = $baseUrl;
        $this->config = $config;
        $this->viewService = $viewService;
        $this->fileUrlMap = $fileUrlMap;
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
        foreach ($this->fileUrlMap as $urlMap) {
            $dir = $this->filesystem->getPath($urlMap['value']);
            $publicFilePath = str_replace('\\', '/', $publicFilePath);
            if (strpos($publicFilePath, $dir) === 0) {
                $relativePath = ltrim(substr($publicFilePath, strlen($dir)), '\\/');
                $url = $this->baseUrl->getBaseUrl(
                        array(
                            '_type' => $urlMap['key'],
                            '_secure' => $isSecure
                        )
                    ) . $relativePath;

                if ($this->isStaticFilesSigned() && $this->viewService->isViewFileOperationAllowed()) {
                    $directory = $this->filesystem->getDirectoryRead(\Magento\App\Filesystem::ROOT_DIR);
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
    protected function isStaticFilesSigned()
    {
        return (bool)$this->config->getValue(self::XML_PATH_STATIC_FILE_SIGNATURE);
    }
}
