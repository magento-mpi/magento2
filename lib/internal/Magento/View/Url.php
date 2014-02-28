<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View;

use Magento\UrlInterface;

/**
 * Builds URLs for publicly accessible files
 */
class Url
{
    /**
     * @var Service
     */
    private $service;

    /**
     * @var \Magento\UrlInterface
     */
    protected $baseUrl;

    /**
     * @var \Magento\View\Path
     */
    protected $path;

    /**
     * @param Service $service
     * @param UrlInterface $baseUrl
     * @param Path $path
     */
    public function __construct(Service $service, UrlInterface $baseUrl, Path $path)
    {
        $this->service = $service;
        $this->baseUrl = $baseUrl;
        $this->path = $path;
    }

    /**
     * Retrieve view file URL
     *
     * Get URL to file base on theme file identifier.
     *
     * @param string $fileId
     * @param array $params
     * @return string
     */
    public function getViewFileUrl($fileId, array $params = array())
    {
        list($module, $filePath) = Service::extractModule($fileId);
        $params['module'] = $module;

        $this->service->updateDesignParams($params);
        $isSecure = isset($params['_secure']) ? (bool) $params['_secure'] : null;
        $baseUrl = $this->baseUrl->getBaseUrl(array('_type' => UrlInterface::URL_TYPE_STATIC, '_secure' => $isSecure));
        $relPath = $this->path->getRelativePath(
            $params['area'], $params['themeModel'], $params['locale'], $params['module']
        );

        return $baseUrl . $relPath . '/' . $filePath;
    }
}
