<?php
/**
 * Url Rewrite front controller plugin. Performs url rewrites
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\App\FrontController\Plugin;

class UrlRewrite
{
    /**
     * @var \Magento\Core\App\Request\RewriteService RewriteService
     */
    protected $_rewriteService;

    /**
     * @var \Magento\App\State
     */
    protected $_state;

    /**
     * @param \Magento\Core\App\Request\RewriteService $rewriteService
     * @param \Magento\App\State $state
     */
    public function __construct(
        \Magento\Core\App\Request\RewriteService $rewriteService,
        \Magento\App\State $state
    ) {
        $this->_rewriteService = $rewriteService;
        $this->_state = $state;
    }

    /**
     * Perform url rewites
     *
     * @param array $arguments
     * @param \Magento\Code\Plugin\InvocationChain $invocationChain
     * @return mixed
     */
    public function aroundDispatch(array $arguments, \Magento\Code\Plugin\InvocationChain $invocationChain)
    {
        if (!$this->_state->isInstalled()) {
            return $invocationChain->proceed($arguments);
        }
        $request = $arguments[0];
        $this->_rewriteService->applyRewrites($request);
        return $invocationChain->proceed($arguments);
    }
}
