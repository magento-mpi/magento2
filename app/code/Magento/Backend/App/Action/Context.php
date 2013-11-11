<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\App\Action;

/**
 * Controller context
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Context extends \Magento\App\Action\Context
{
    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $_session;

    /**
     * @var \Magento\Backend\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Magento\AuthorizationInterface
     */
    protected $_authorization;

    /**
     * @var \Magento\Core\Model\Translate
     */
    protected $_translator;

    /**
     * @var \Magento\Backend\Model\Auth
     */
    protected $_auth;

    /**
     * @var \Magento\Backend\Model\Url
     */
    protected $_backendUrl;

    /**
     * @var \Magento\Core\Model\LocaleInterface
     */
    protected $_locale;

    /**
     * @var bool
     */
    protected $_canUseBaseUrl;

    /**
     * @param \Magento\Logger $logger
     * @param \Magento\App\RequestInterface $request
     * @param \Magento\App\ResponseInterface $response
     * @param \Magento\ObjectManager $objectManager
     * @param \Magento\App\FrontController $frontController
     * @param \Magento\View\LayoutInterface $layout
     * @param \Magento\Event\ManagerInterface $eventManager
     * @param \Magento\HTTP\Authentication $authentication
     * @param \Magento\App\State $appState
     * @param \Magento\Filesystem $filesystem
     * @param \Magento\Config\ScopeInterface $configScope
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Core\Model\LocaleInterface $locale
     * @param \Magento\Core\Model\Url $url
     * @param \Magento\Core\Model\Translate $translate
     * @param \Magento\Core\Model\Store\Config $storeConfig
     * @param \Magento\Core\Model\Cookie $cookie
     * @param \Magento\Core\Model\App $app
     * @param $isRenderInherited
     * @param \Magento\Backend\Model\Session $session
     * @param \Magento\Backend\Helper\Data $helper
     * @param \Magento\App\ActionFlag $flag
     * @param \Magento\AuthorizationInterface $authorization
     * @param \Magento\Core\Model\Translate $translator
     * @param \Magento\Backend\Model\Auth $auth
     * @param \Magento\Backend\Model\Url $backendUrl
     * @param \Magento\Core\Model\LocaleInterface $locale
     * @param bool $canUseBaseUrl
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Logger $logger,
        \Magento\App\RequestInterface $request,
        \Magento\App\ResponseInterface $response,
        \Magento\ObjectManager $objectManager,
        \Magento\App\FrontController $frontController,
        \Magento\View\LayoutInterface $layout,
        \Magento\Event\ManagerInterface $eventManager,
        \Magento\HTTP\Authentication $authentication,
        \Magento\App\State $appState,
        \Magento\Filesystem $filesystem,
        \Magento\Config\ScopeInterface $configScope,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Core\Model\LocaleInterface $locale,
        \Magento\Core\Model\Url $url,
        \Magento\Core\Model\Translate $translate,
        \Magento\Core\Model\Store\Config $storeConfig,
        \Magento\Core\Model\Cookie $cookie,
        \Magento\Core\Model\App $app,
        $isRenderInherited,
        \Magento\Backend\Model\Session $session,
        \Magento\Backend\Helper\Data $helper,
        \Magento\App\ActionFlag $flag,
        \Magento\AuthorizationInterface $authorization,
        \Magento\Core\Model\Translate $translator,
        \Magento\Backend\Model\Auth $auth,
        \Magento\Backend\Model\Url $backendUrl,
        \Magento\Core\Model\LocaleInterface $locale,
        $canUseBaseUrl = false
    ) {
        parent::__construct(
            $logger, $request, $response, $objectManager, $frontController, $layout, $eventManager, $authentication,
            $appState, $filesystem, $configScope, $storeManager, $locale, $session, $url, $translate,
            $storeConfig, $cookie, $app, $helper, $flag, $isRenderInherited
        );
        $this->_canUseBaseUrl = $canUseBaseUrl;
        $this->_session = $session;
        $this->_helper = $helper;
        $this->_authorization = $authorization;
        $this->_translator = $translator;
        $this->_auth = $auth;
        $this->_backendUrl = $backendUrl;
        $this->_locale = $locale;
    }

    /**
     * @return \Magento\AuthorizationInterface
     */
    public function getAuthorization()
    {
        return $this->_authorization;
    }

    /**
     * @return \Magento\Core\Model\Translate
     */
    public function getTranslator()
    {
        return $this->_translator;
    }

    /**
     * @return \Magento\Backend\Model\Auth
     */
    public function getAuth()
    {
        return $this->_auth;
    }

    /**
     * @return \Magento\Backend\Model\Url
     */
    public function getBackendUrl()
    {
        return $this->_backendUrl;
    }

    /**
     * @return \Magento\Core\Model\LocaleInterface
     */
    public function getLocale()
    {
        return $this->_locale;
    }

    /**
     * @return boolean
     */
    public function getCanUseBaseUrl()
    {
        return $this->_canUseBaseUrl;
    }
}
