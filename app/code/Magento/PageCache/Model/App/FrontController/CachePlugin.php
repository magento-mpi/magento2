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
     * @param \Magento\PageCache\Model\Config $config
     * @param \Magento\App\PageCache\Version $version
     * @param \Magento\App\PageCache\Kernel $kernel
     */
    public function __construct(
        \Magento\PageCache\Model\Config $config,
        \Magento\App\PageCache\Version $version,
        \Magento\App\PageCache\Kernel $kernel
    ) {
        $this->config = $config;
        $this->version = $version;
        $this->kernel = $kernel;
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
                $this->kernel->process($response);
            }
        } else {
            $response = $invocationChain->proceed($arguments);
        }
        return $response;
    }
}
