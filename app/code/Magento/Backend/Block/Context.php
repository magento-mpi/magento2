<?php

namespace Magento\Backend\Block;

/**
 * Backend block context
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Context extends \Magento\Core\Block\Context
{
    /**
     * @var \Magento\AuthorizationInterface
     */
    protected $_authorization;

    /**
     * @param \Magento\App\RequestInterface $request
     * @param \Magento\Core\Model\Layout $layout
     * @param \Magento\Event\ManagerInterface $eventManager
     * @param \Magento\Core\Model\UrlInterface $urlBuilder
     * @param \Magento\Core\Model\Translate $translator
     * @param \Magento\Core\Model\CacheInterface $cache
     * @param \Magento\Core\Model\View\DesignInterface $design
     * @param \Magento\Core\Model\Session\AbstractSession $session
     * @param \Magento\Core\Model\Store\Config $storeConfig
     * @param \Magento\App\FrontController $frontController
     * @param \Magento\Core\Model\Factory\Helper $helperFactory
     * @param \Magento\Core\Model\View\Url $viewUrl
     * @param \Magento\Core\Model\View\Config $viewConfig
     * @param \Magento\Core\Model\Cache\StateInterface $cacheState
     * @param \Magento\AuthorizationInterface $authorization
     * @param \Magento\Core\Model\Logger $logger
     * @param \Magento\Core\Model\App $app
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\App\RequestInterface $request,
        \Magento\Core\Model\Layout $layout,
        \Magento\Event\ManagerInterface $eventManager,
        \Magento\Core\Model\UrlInterface $urlBuilder,
        \Magento\Core\Model\Translate $translator,
        \Magento\Core\Model\CacheInterface $cache,
        \Magento\Core\Model\View\DesignInterface $design,
        \Magento\Core\Model\Session\AbstractSession $session,
        \Magento\Core\Model\Store\Config $storeConfig,
        \Magento\App\FrontController $frontController,
        \Magento\Core\Model\Factory\Helper $helperFactory,
        \Magento\Core\Model\View\Url $viewUrl,
        \Magento\Core\Model\View\Config $viewConfig,
        \Magento\Core\Model\Cache\StateInterface $cacheState,
        \Magento\AuthorizationInterface $authorization,
        \Magento\Core\Model\Logger $logger,
        \Magento\Core\Model\App $app,
        array $data = array()
    ) {
        $this->_authorization = $authorization;
        parent::__construct($request, $layout, $eventManager, $urlBuilder, $translator, $cache, $design,
            $session, $storeConfig, $frontController, $helperFactory, $viewUrl, $viewConfig, $cacheState, $logger, $app,
            $data
        );
    }

    /**
     * Retrieve Authorization
     *
     * @return \Magento\AuthorizationInterface
     */
    public function getAuthorization()
    {
        return $this->_authorization;
    }
}
