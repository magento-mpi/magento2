<?php

namespace Magento\Backend\Block\Template;

/**
 * Backend block template context
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Context extends \Magento\View\Element\Template\Context
{
    /**
     * @var \Magento\AuthorizationInterface
     */
    protected $_authorization;

    /**
     * @var \Magento\Math\Random
     */
    protected $mathRandom;

    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $_backendSession;

    /**
     * @var \Magento\Data\Form\FormKey
     */
    protected $formKey;

    /**
     * @var \Magento\Code\NameBuilder
     */
    protected $nameBuilder;

    /**
     * @param \Magento\App\RequestInterface $request
     * @param \Magento\View\LayoutInterface $layout
     * @param \Magento\Event\ManagerInterface $eventManager
     * @param \Magento\UrlInterface $urlBuilder
     * @param \Magento\TranslateInterface $translator
     * @param \Magento\App\CacheInterface $cache
     * @param \Magento\View\DesignInterface $design
     * @param \Magento\Core\Model\Session $session
     * @param \Magento\Session\SidResolverInterface $sidResolver
     * @param \Magento\Core\Model\Store\Config $storeConfig
     * @param \Magento\App\FrontController $frontController
     * @param \Magento\View\Url $viewUrl
     * @param \Magento\View\ConfigInterface $viewConfig
     * @param \Magento\App\Cache\StateInterface $cacheState
     * @param \Magento\Logger $logger
     * @param \Magento\Core\Model\App $app
     * @param \Magento\Escaper $escaper
     * @param \Magento\Filter\FilterManager $filterManager
     * @param \Magento\Core\Model\LocaleInterface $locale
     * @param \Magento\App\Filesystem $filesystem
     * @param \Magento\View\FileSystem $viewFileSystem
     * @param \Magento\View\TemplateEnginePool $enginePool
     * @param \Magento\App\State $appState
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\AuthorizationInterface $authorization
     * @param \Magento\Backend\Model\Session $backendSession
     * @param \Magento\Math\Random $mathRandom
     * @param \Magento\Data\Form\FormKey $formKey
     * @param \Magento\Code\NameBuilder $nameBuilder
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\App\RequestInterface $request,
        \Magento\View\LayoutInterface $layout,
        \Magento\Event\ManagerInterface $eventManager,
        \Magento\UrlInterface $urlBuilder,
        \Magento\TranslateInterface $translator,
        \Magento\App\CacheInterface $cache,
        \Magento\View\DesignInterface $design,
        \Magento\Core\Model\Session $session,
        \Magento\Session\SidResolverInterface $sidResolver,
        \Magento\Core\Model\Store\Config $storeConfig,
        \Magento\App\FrontController $frontController,
        \Magento\View\Url $viewUrl,
        \Magento\View\ConfigInterface $viewConfig,
        \Magento\App\Cache\StateInterface $cacheState,
        \Magento\Logger $logger,
        \Magento\Core\Model\App $app,
        \Magento\Escaper $escaper,
        \Magento\Filter\FilterManager $filterManager,
        \Magento\Core\Model\LocaleInterface $locale,
        \Magento\App\Filesystem $filesystem,
        \Magento\View\FileSystem $viewFileSystem,
        \Magento\View\TemplateEnginePool $enginePool,
        \Magento\App\State $appState,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\AuthorizationInterface $authorization,
        \Magento\Backend\Model\Session $backendSession,
        \Magento\Math\Random $mathRandom,
        \Magento\Data\Form\FormKey $formKey,
        \Magento\Code\NameBuilder $nameBuilder,
        array $data = array()
    ) {
        $this->_authorization = $authorization;
        $this->_backendSession = $backendSession;
        $this->mathRandom = $mathRandom;
        $this->formKey = $formKey;
        $this->nameBuilder = $nameBuilder;
        parent::__construct(
            $request,
            $layout,
            $eventManager,
            $urlBuilder,
            $translator,
            $cache,
            $design,
            $session,
            $sidResolver,
            $storeConfig,
            $frontController,
            $viewUrl,
            $viewConfig,
            $cacheState,
            $logger,
            $app,
            $escaper,
            $filterManager,
            $locale,
            $filesystem,
            $viewFileSystem,
            $enginePool,
            $appState,
            $storeManager,
            $data
        );
    }

    /**
     * Get store manager
     *
     * @return \Magento\Core\Model\StoreManagerInterface
     */
    public function getStoreManager()
    {
        return $this->_storeManager;
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

    /**
     * @return \Magento\Backend\Model\Session
     */
    public function getBackendSession()
    {
        return $this->_backendSession;
    }

    /**
     * @return \Magento\Core\Model\LocaleInterface
     */
    public function getLocale()
    {
        return $this->_locale;
    }

    /**
     * @return \Magento\Math\Random
     */
    public function getMathRandom()
    {
        return $this->mathRandom;
    }

    /**
     * @return \Magento\Data\Form\FormKey
     */
    public function getFormKey()
    {
        return $this->formKey;
    }

    /**
     * @return \Magento\Data\Form\FormKey
     */
    public function getNameBuilder()
    {
        return $this->nameBuilder;
    }
}
