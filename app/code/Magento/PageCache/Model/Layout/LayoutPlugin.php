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
     * @var \Magento\View\Layout
     */
    protected $layout;

    /**
     * @var \Magento\PageCache\Model\Config
     */
    protected $config;

    /**
     * @var \Magento\App\ResponseInterface
     */
    protected $response;

    /**
     * Constructor
     *
     * @param \Magento\View\Layout $layout
     * @param \Magento\App\ResponseInterface $response
     * @param \Magento\PageCache\Model\Config $config
     */
    public function __construct(
        \Magento\View\Layout $layout,
        \Magento\App\ResponseInterface $response,
        \Magento\PageCache\Model\Config $config
    ) {
        $this->layout = $layout;
        $this->response = $response;
        $this->config = $config;
    }

    /**
     * Set appropriate Cache-Control headers
     * We have to set public headers in order to tell Varnish and Builtin app that page should be cached
     *
     * @param \Magento\View\Layout $subject
     * @param mixed $result
     * @return mixed
     */
    public function afterGenerateXml(\Magento\View\Layout $subject, $result)
    {
        if ($this->layout->isCacheable() && $this->config->isEnabled()) {
            $this->response->setPublicHeaders($this->config->getTtl());
        }
        return $result;
    }

    /**
     * Retrieve all identities from blocks for further cache invalidation
     *
     * @param \Magento\View\Layout $subject
     * @param mixed $result
     * @return mixed
     */
    public function afterGetOutput(\Magento\View\Layout $subject, $result)
    {
        if ($this->layout->isCacheable() && $this->config->isEnabled()) {
            $tags = array();
            foreach ($this->layout->getAllBlocks() as $block) {
                if ($block instanceof \Magento\View\Block\IdentityInterface) {
                    $isEsiBlock = $block->getTtl() > 0;
                    $isVarnish = $this->config->getType() == \Magento\PageCache\Model\Config::VARNISH;
                    if ($isVarnish && $isEsiBlock) {
                        continue;
                    }
                    $tags = array_merge($tags, $block->getIdentities());
                }
            }
            $tags = array_unique($tags);
            $this->response->setHeader('X-Magento-Tags', implode(',', $tags));
        }
        return $result;
    }
}
