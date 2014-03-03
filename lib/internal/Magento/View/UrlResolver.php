<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View;

/**
 * Class for resolving URLs by file paths
 */
class UrlResolver
{
    /**
     * @var \Magento\App\Filesystem
     */
    protected $filesystem;

    /**
     * @var \Magento\UrlInterface
     */
    protected $baseUrl;

    /**
     * Map urls to app dirs
     *
     * @var array
     */
    protected $fileUrlMap;

    /**
     * @param \Magento\App\Filesystem $filesystem
     * @param \Magento\UrlInterface $baseUrl
     * @param array $fileUrlMap
     */
    public function __construct(
        \Magento\App\Filesystem $filesystem,
        \Magento\UrlInterface $baseUrl,
        array $fileUrlMap = array()
    ) {
        $this->filesystem = $filesystem;
        $this->baseUrl = $baseUrl;
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
                return $url;
            }
        }
        throw new \Magento\Exception(
            "Cannot build URL for the file '$publicFilePath' because it does not reside in a public directory."
        );
    }
}
