<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\PageCache\Model\App\FrontController;

/**
 * Plugin for processing builtin cache
 */
class CachePlugin
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
     * @var \Magento\App\PageCache\Kernel
     */
    protected $kernel;

    /**
     * @var \Magento\App\State
     */
    protected $state;

    /**
     * @param \Magento\PageCache\Model\Config $config
     * @param \Magento\App\PageCache\Version $version
     * @param \Magento\App\PageCache\Kernel $kernel
     * @param \Magento\App\State $state
     */
    public function __construct(
        \Magento\PageCache\Model\Config $config,
        \Magento\App\PageCache\Version $version,
        \Magento\App\PageCache\Kernel $kernel,
        \Magento\App\State $state
    ) {
        $this->config = $config;
        $this->version = $version;
        $this->kernel = $kernel;
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
        $this->version->process();
        if ($this->config->getType() == \Magento\PageCache\Model\Config::BUILT_IN) {
            $response = $this->kernel->load();
            if ($response === false) {
                $response = $invocationChain->proceed($arguments);
                if ($this->isDebug()) {
                    $response->setHeader('X-Magento-Cache-Control', $response->getHeader('Cache-Control')['value']);
                }
                $this->kernel->process($response);
                if ($this->isDebug()) {
                    $response->setHeader('X-Magento-Cache-Debug', 'MISS');
                }
            } elseif ($this->isDebug()) {
                $response->setHeader('X-Magento-Cache-Debug', 'HIT');
            }
        } else {
            $response = $invocationChain->proceed($arguments);
            if ($this->isDebug()) {
                $response->setHeader('X-Magento-Cache-Control', $response->getHeader('Cache-Control')['value']);
            }
        }

        return $response;
    }

    /**
     * @return bool
     */
    protected function isDebug()
    {
        return $this->state->getMode() == \Magento\App\State::MODE_DEVELOPER;
    }
}
