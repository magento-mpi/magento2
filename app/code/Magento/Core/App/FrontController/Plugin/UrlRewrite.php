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
     * @param \Magento\App\FrontController $subject
     * @param callable $proceed
     * @param \Magento\App\RequestInterface $request
     *
     * @return \Magento\App\ResponseInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundDispatch(
        \Magento\App\FrontController $subject,
        \Closure $proceed,
        \Magento\App\RequestInterface $request
    ) {
        if (!$this->_state->isInstalled()) {
            return $proceed($request);
        }
        $this->_rewriteService->applyRewrites($request);
        return $proceed($request);
    }
}
