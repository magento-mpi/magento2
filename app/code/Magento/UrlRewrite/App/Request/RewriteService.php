<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\UrlRewrite\App\Request;

class RewriteService
{
    /**
     * @var \Magento\UrlRewrite\Model\UrlRewriteFactory
     */
    protected $_rewriteFactory;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_config;

    /**
     * @var \Magento\Framework\App\RouterList
     */
    protected $_routerList;

    /**
     * @param \Magento\Framework\App\RouterList $routerList
     * @param \Magento\UrlRewrite\Model\UrlRewriteFactory $rewriteFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     */
    public function __construct(
        \Magento\Framework\App\RouterList $routerList,
        \Magento\UrlRewrite\Model\UrlRewriteFactory $rewriteFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $config
    ) {
        $this->_rewriteFactory = $rewriteFactory;
        $this->_config = $config;
        $this->_routerList = $routerList;
    }

    /**
     * Apply rewrites to current request
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return void
     */
    public function applyRewrites(\Magento\Framework\App\RequestInterface $request)
    {
        // URL rewrite
        if (!$request->isStraight()) {
            \Magento\Profiler::start('db_url_rewrite');
            /** @var $urlRewrite \Magento\UrlRewrite\Model\UrlRewrite */
            $urlRewrite = $this->_rewriteFactory->create();
            $urlRewrite->rewrite($request);
            \Magento\Profiler::stop('db_url_rewrite');
        }
    }
}
