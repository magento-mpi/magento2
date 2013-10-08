<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Request;

class RewriteService
{
    /**
     * @var \Magento\Core\Model\Url\RewriteFactory
     */
    protected $_rewriteFactory;

    /**
     * @var \Magento\Core\Model\Config
     */
    protected $_config;

    /**
     * @var \Magento\App\RouterList
     */
    protected $_routerList;

    public function __construct(
        \Magento\App\RouterList $routerList,
        \Magento\Core\Model\Url\RewriteFactory $rewriteFactory,
        \Magento\Core\Model\Config $config
    ) {
        $this->_rewriteFactory = $rewriteFactory;
        $this->_config = $config;
        $this->_routerList = $routerList;
    }

    /**
     * Apply rewrites to current request
     *
     * @param \Magento\App\RequestInterface $request
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

        // config rewrite
        \Magento\Profiler::start('config_url_rewrite');
        $this->rewrite($request);
        \Magento\Profiler::stop('config_url_rewrite');
    }

    /**
     * Apply configuration rewrites to current url
     *
     * @param \Magento\App\RequestInterface $request
     */
    public function rewrite(\Magento\App\RequestInterface $request = null)
    {
        $config = $this->_config->getNode('global/rewrite');
        if (!$config) {
            return;
        }
        foreach ($config->children() as $rewrite) {
            $from = (string)$rewrite->from;
            $to = (string)$rewrite->to;
            if (empty($from) || empty($to)) {
                continue;
            }
            $from = $this->_processRewriteUrl($from);
            $to   = $this->_processRewriteUrl($to);

            $pathInfo = preg_replace($from, $to, $request->getPathInfo());

            if (isset($rewrite->complete)) {
                $request->setPathInfo($pathInfo);
            } else {
                $request->rewritePathInfo($pathInfo);
            }
        }
    }


    /**
     * Replace route name placeholders in url to front name
     *
     * @param   string $url
     * @return  string
     */
    protected function _processRewriteUrl($url)
    {
        $startPos = strpos($url, '{');
        if ($startPos!==false) {
            $endPos = strpos($url, '}');
            $routeId = substr($url, $startPos+1, $endPos-$startPos-1);
            $router = $this->_routerList->getRouterByRoute($routeId);
            if ($router) {
                $frontName = $router->getFrontNameByRoute($routeId);
                $url = str_replace('{'.$routeId.'}', $frontName, $url);
            }
        }
        return $url;
    }

}