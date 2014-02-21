<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\PageCache\Model\App\FrontController;

/**
 * Varnish for processing builtin cache
 */
class VarnishPlugin
{
    /**
     * @var \Magento\App\ConfigInterface
     */
    protected $config;

    /**
     * @var \Magento\App\PageCache\Version
     */
    protected $version;

    /**
     * @var \Magento\App\State
     */
    protected $state;

    /**
     * @param \Magento\PageCache\Model\Config $config
     * @param \Magento\App\PageCache\Version $version
     * @param \Magento\App\State $state
     */
    public function __construct(
        \Magento\PageCache\Model\Config $config,
        \Magento\App\PageCache\Version $version,
        \Magento\App\State $state
    ) {
        $this->config = $config;
        $this->version = $version;
        $this->state = $state;
    }

    /**
     * Try load response from cache and preventing application from being processing if cache hit
     *
     * @param array $arguments
     * @param \Magento\Code\Plugin\InvocationChain $invocationChain
     * @return \Magento\App\Response\Http
     */
    public function aroundDispatch(array $arguments, \Magento\Code\Plugin\InvocationChain $invocationChain)
    {
        if ($this->config->getType() === \Magento\PageCache\Model\Config::VARNISH) {
            $this->version->process();
            $response = $invocationChain->proceed($arguments);
            if ($this->state->getMode() == \Magento\App\State::MODE_DEVELOPER) {
                $response->setHeader('X-Magento-Cache-Control', $response->getHeader('Cache-Control')['value']);
            }
        } else {
            $response = $invocationChain->proceed($arguments);
        }
        return $response;
    }
}
