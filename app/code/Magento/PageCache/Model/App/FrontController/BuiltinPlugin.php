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
class BuiltinPlugin
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
     * @param \Magento\App\FrontControllerInterface $subject
     * @param callable $proceed
     * @param \Magento\App\RequestInterface $request
     * @return false|\Magento\App\Response\Http
     */
    public function aroundDispatch(
        \Magento\App\FrontControllerInterface $subject,
        \Closure $proceed,
        \Magento\App\RequestInterface $request
    ) {
        if ($this->config->getType() == \Magento\PageCache\Model\Config::BUILT_IN && $this->config->isEnabled()) {
            $this->version->process();
            $response = $this->kernel->load();
            if ($response === false) {
                $response = $proceed($request);
                $cacheControl = $response->getHeader('Cache-Control')['value'];
                $this->addDebugHeader($response, 'X-Magento-Cache-Control', $cacheControl);
                $this->kernel->process($response);
                $this->addDebugHeader($response, 'X-Magento-Cache-Debug', 'MISS');
            } else {
                $this->addDebugHeader($response, 'X-Magento-Cache-Debug', 'HIT');
            }
        } else {
            return $response = $proceed($request);
        }
        return $response;
    }

    /**
     * Add additional header for debug purpose
     *
     * @param \Magento\App\Response\Http $response
     * @param string $name
     * @param string $value
     * @return void
     */
    protected function addDebugHeader(\Magento\App\Response\Http $response, $name, $value)
    {
        if ($this->state->getMode() == \Magento\App\State::MODE_DEVELOPER) {
            $response->setHeader($name, $value);
        }
    }
}
