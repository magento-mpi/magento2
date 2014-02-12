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
     * @var \Magento\App\ResponseInterface
     */
    protected $response;

    /**
     * Constructor
     *
     * @param \Magento\Core\Model\Layout $layout
     * @param \Magento\App\ResponseInterface $response
     * @param \Magento\App\ConfigInterface $config
     */
    public function __construct(
        \Magento\Core\Model\Layout $layout,
        \Magento\App\ResponseInterface $response,
        \Magento\App\ConfigInterface $config
    ) {
        $this->layout = $layout;
        $this->response = $response;
        $this->config = $config;
    }

    public function afterGenerateXml()
    {
        $varnishIsEnabled = $this->config->isSetFlag(\Magento\PageCache\Model\Config::XML_PAGECACHE_TYPE);
        if ($varnishIsEnabled) {
            if ($this->layout->isCacheable()) {
                $maxAge = $this->config->getValue(\Magento\PageCache\Model\Config::XML_PAGECACHE_TTL);
                $this->response->setPublicHeaders($maxAge);
            } else {
                $this->response->setNoCacheHeaders();
            }
        }
    }
}
