<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\PageCache\Model\Layout;

/**
 * Class LayoutPlugin
 */
class LayoutPlugin
{
    /**
     * @var \Magento\Core\Model\Layout
     */
    protected $layout;

    /**
     * @var \Magento\App\ConfigInterface
     */
    protected $config;

    /**
     * @var \Magento\PageCache\Model\Version
     */
    protected $version;

    /**
     * @var \Magento\App\ResponseInterface
     */
    protected $response;

    /**
     * Constructor
     *
     * @param \Magento\Core\Model\Layout       $layout
     * @param \Magento\App\ResponseInterface   $response
     * @param \Magento\App\ConfigInterface     $config
     * @param \Magento\PageCache\Model\Version $version
     */
    public function __construct(
        \Magento\Core\Model\Layout $layout,
        \Magento\App\ResponseInterface $response,
        \Magento\App\ConfigInterface $config,
        \Magento\PageCache\Model\Version $version
    ) {
        $this->layout = $layout;
        $this->response = $response;
        $this->config = $config;
        $this->version = $version;
    }

    public function afterGenerateXml()
    {
        $varnishIsEnabled = $this->config->isSetFlag(\Magento\PageCache\Model\Config::XML_PATH_VARNISH_ENABLED);
        if ($varnishIsEnabled) {
            if (!$this->layout->isCacheable()) {
                $maxAge = $this->config->getValue(\Magento\PageCache\Model\Config::XML_VARNISH_PAGECACHE_TTL);
                $this->response->setPublicHeaders($maxAge);
            } else {
                $this->response->setNoCacheHeaders(true);
            }
            $this->version->process();
        }
    }
}
