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
     * @param \Magento\UrlRewrite\App\Request\RewriteService $rewriteService
     */
    public function __construct(
        \Magento\UrlRewrite\App\Request\RewriteService $rewriteService
    ) {
        $this->_rewriteService = $rewriteService;
    }

    /**
     * Perform url rewrites
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
        $this->_rewriteService->applyRewrites($request);
        return $proceed($request);
    }
}
