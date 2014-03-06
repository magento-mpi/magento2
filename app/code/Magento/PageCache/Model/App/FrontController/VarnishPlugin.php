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
        if ($this->config->getType() == \Magento\PageCache\Model\Config::VARNISH) {
            $this->version->process();
            $response = $proceed($request);
            if ($this->state->getMode() == \Magento\App\State::MODE_DEVELOPER) {
                $response->setHeader('X-Magento-Debug', 1);
            }
        } else {
            $response = $proceed($request);
        }
        return $response;
    }
}
