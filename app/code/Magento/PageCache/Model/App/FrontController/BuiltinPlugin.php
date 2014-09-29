<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\PageCache\Model\App\FrontController;

use Magento\Framework\App\Response\Http as ResponseHttp;

/**
 * Plugin for processing builtin cache
 */
class BuiltinPlugin
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $config;

    /**
     * @var \Magento\Framework\App\PageCache\Version
     */
    protected $version;

    /**
     * @var \Magento\Framework\App\PageCache\Kernel
     */
    protected $kernel;

    /**
     * @var \Magento\Framework\App\State
     */
    protected $state;

    /**
     * @param \Magento\PageCache\Model\Config $config
     * @param \Magento\Framework\App\PageCache\Version $version
     * @param \Magento\Framework\App\PageCache\Kernel $kernel
     * @param \Magento\Framework\App\State $state
     */
    public function __construct(
        \Magento\PageCache\Model\Config $config,
        \Magento\Framework\App\PageCache\Version $version,
        \Magento\Framework\App\PageCache\Kernel $kernel,
        \Magento\Framework\App\State $state
    ) {
        $this->config = $config;
        $this->version = $version;
        $this->kernel = $kernel;
        $this->state = $state;
    }

    /**
     * @param \Magento\Framework\App\FrontControllerInterface $subject
     * @param callable $proceed
     * @param \Magento\Framework\App\RequestInterface $request
     * @return false|\Magento\Framework\App\Response\Http
     */
    public function aroundDispatch(
        \Magento\Framework\App\FrontControllerInterface $subject,
        \Closure $proceed,
        \Magento\Framework\App\RequestInterface $request
    ) {
        if ($this->config->getType() == \Magento\PageCache\Model\Config::BUILT_IN && $this->config->isEnabled()) {
            $this->version->process();
            $result = $this->kernel->load();
            if ($result === false) {
                $result = $proceed($request);
                if ($result instanceof ResponseHttp) {
                    $cacheControl = $result->getHeader('Cache-Control')['value'];
                    $this->addDebugHeader($result, 'X-Magento-Cache-Control', $cacheControl);
                    $this->kernel->process($result);
                    $this->addDebugHeader($result, 'X-Magento-Cache-Debug', 'MISS');
                }
            } else {
                $this->addDebugHeader($result, 'X-Magento-Cache-Debug', 'HIT');
            }
        } else {
            return $result = $proceed($request);
        }
        return $result;
    }

    /**
     * Add additional header for debug purpose
     *
     * @param ResponseHttp $response
     * @param string $name
     * @param string $value
     * @return void
     */
    protected function addDebugHeader(ResponseHttp $response, $name, $value)
    {
        if ($this->state->getMode() == \Magento\Framework\App\State::MODE_DEVELOPER) {
            $response->setHeader($name, $value);
        }
    }
}
