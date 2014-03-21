<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\App\Request;

class RewriteService
{
    /**
     * @var \Magento\Core\Model\Url\RewriteFactory
     */
    protected $_rewriteFactory;

    /**
     * @var \Magento\App\Config\ScopeConfigInterface
     */
    protected $_config;

    /**
     * @var \Magento\App\RouterList
     */
    protected $_routerList;

    /**
     * @param \Magento\App\RouterList $routerList
     * @param \Magento\Core\Model\Url\RewriteFactory $rewriteFactory
     * @param \Magento\App\Config\ScopeConfigInterface $config
     */
    public function __construct(
        \Magento\App\RouterList $routerList,
        \Magento\Core\Model\Url\RewriteFactory $rewriteFactory,
        \Magento\App\Config\ScopeConfigInterface $config
    ) {
        $this->_rewriteFactory = $rewriteFactory;
        $this->_config = $config;
        $this->_routerList = $routerList;
    }

    /**
     * Apply rewrites to current request
     *
     * @param \Magento\App\RequestInterface $request
     * @return void
     */
    public function applyRewrites(\Magento\App\RequestInterface $request)
    {
        // URL rewrite
        if (!$request->isStraight()) {
            \Magento\Profiler::start('db_url_rewrite');
            /** @var $urlRewrite \Magento\Core\Model\Url\Rewrite */
            $urlRewrite = $this->_rewriteFactory->create();
            $urlRewrite->rewrite($request);
            \Magento\Profiler::stop('db_url_rewrite');
        }
    }
}