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
     * @var \Magento\App\Config\ScopeConfigInterface
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
     * @param \Magento\App\FrontControllerInterface $subject
     * @param \Closure $proceed
     * @param \Magento\App\RequestInterface $request
     * @return false|\Magento\App\Response\Http
     */
    public function aroundDispatch(
        \Magento\App\FrontControllerInterface $subject,
        \Closure $proceed,
        \Magento\App\RequestInterface $request
    ) {
        $this->version->process();
        if ($this->config->getType() == \Magento\PageCache\Model\Config::BUILT_IN) {
            $response = $this->kernel->load();
            if ($response === false) {
                $response = $proceed($request);
                $this->kernel->process($response);
            }
        } else {
            $response = $proceed($request);
        }
        return $response;
    }
}
