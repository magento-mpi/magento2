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

    /**
     * Set appropriate Cache-Control headers
     * We have to set public headers in order to tell Varnish and Builtin app that page should be cached
     *
     * @param \Magento\Core\Model\Layout $layout
     * @return \Magento\Core\Model\Layout
     */
    public function afterGenerateXml($layout)
    {
        if ($this->layout->isCacheable()) {
            $maxAge = $this->config->getValue(\Magento\PageCache\Model\Config::XML_PAGECACHE_TTL);
            $this->response->setPublicHeaders($maxAge);
        }
        return $layout;
    }

    /**
     * Retrieve all identities from blocks for further cache invalidation
     *
     * @param string $html
     * @return string
     */
    public function afterGetOutput($html)
    {
        if ($this->layout->isCacheable()) {
            $tags = array();
            foreach($this->layout->getAllBlocks() as $block) {
                if ($block instanceof \Magento\View\Block\IdentityInterface) {
                    $tags += $block->getIdentities();
                }
            }
            $this->response->setHeader('X-Magento-Tags', implode(',', $tags));
        }
        return $html;
    }
}
