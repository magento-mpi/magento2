<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\PageCache\Model\App\FrontController;

use Magento\PageCache\Helper\Data;

/**
 * Class HeadPlugin
 */
class HeaderPlugin
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
     * Constructor
     *
     * @param \Magento\Core\Model\Layout $layout
     * @param \Magento\App\ConfigInterface $config
     * @param \Magento\PageCache\Helper\Data
     * @param \Magento\PageCache\Model\Version $version
     */
    public function __construct(
        \Magento\Core\Model\Layout $layout,
        \Magento\App\ConfigInterface $config,
        \Magento\PageCache\Model\Version $version
    ) {
        $this->layout = $layout;
        $this->config = $config;
        $this->version = $version;
    }

    /**
     * Modify response after dispatch
     *
     * @param \Magento\App\Response\Http $response
     * @return \Magento\App\Response\Http
     */
    public function afterDispatch(\Magento\App\Response\Http $response)
    {
        if ($this->layout->isPrivate()) {
            $this->setPrivateHeaders($response);
            return $response;
        }
        if ($this->layout->isCacheable()) {
            $this->setPublicHeaders($response);
        } else {
            $this->setNocacheHeaders($response);
        }
        $this->version->process();
        return $response;
    }

    /**
     * @param \Magento\App\Response\Http $response
     */
    protected function setPublicHeaders(\Magento\App\Response\Http $response)
    {
        $maxAge = $this->config->getValue(\Magento\PageCache\Model\Config::XML_VARNISH_PAGECACHE_TTL);
        $response->setHeader('pragma', 'cache', true);
        $response->setHeader('cache-control', 'public, max-age=' . $maxAge, true);
        $response->setHeader('expires', gmdate('D, d M Y H:i:s T', strtotime('+' . $maxAge . ' seconds')), true);
    }

    /**
     * @param \Magento\App\Response\Http $response
     */
    protected function setNocacheHeaders(\Magento\App\Response\Http $response)
    {
        $response->setHeader('pragma', 'no-cache', true);
        $response->setHeader('cache-control', 'no-store, no-cache, must-revalidate, max-age=0', true);
        $response->setHeader('expires', gmdate('D, d M Y H:i:s T', strtotime('-1 year')), true);
    }

    /**
     * Set header parameters for private cache
     *
     * @param \Magento\App\Response\Http $response
     */
    protected function setPrivateHeaders(\Magento\App\Response\Http $response)
    {
        $maxAge = Data::PRIVATE_MAX_AGE_CACHE;
        $response->setHeader('pragma', 'cache', true);
        $response->setHeader('cache-control', 'private, max-age=' . $maxAge, true);
        $response->setHeader('expires', gmdate('D, d M Y H:i:s T', strtotime('+' . $maxAge . ' seconds')), true);
    }
}
