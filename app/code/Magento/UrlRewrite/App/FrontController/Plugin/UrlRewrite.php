<?php
/**
 * Url Rewrite front controller plugin. Performs url rewrites
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\UrlRewrite\App\FrontController\Plugin;

class UrlRewrite
{
    /**
     * @var \Magento\UrlRewrite\App\Request\RewriteService
     */
    protected $_rewriteService;

    /**
     * @var \Magento\Framework\App\State
     */
    protected $_state;

    /**
     * @param \Magento\UrlRewrite\App\Request\RewriteService $rewriteService
     * @param \Magento\Framework\App\State $state
     */
    public function __construct(
        \Magento\UrlRewrite\App\Request\RewriteService $rewriteService,
        \Magento\Framework\App\State $state
    ) {
        $this->_rewriteService = $rewriteService;
        $this->_state = $state;
    }

    /**
     * Perform url rewites
     *
     * @param \Magento\Framework\App\FrontController $subject
     * @param callable $proceed
     * @param \Magento\Framework\App\RequestInterface $request
     *
     * @return \Magento\Framework\App\ResponseInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundDispatch(
        \Magento\Framework\App\FrontController $subject,
        \Closure $proceed,
        \Magento\Framework\App\RequestInterface $request
    ) {
        if (!$this->_state->isInstalled()) {
            return $proceed($request);
        }
        $this->_rewriteService->applyRewrites($request);
        return $proceed($request);
    }
}
