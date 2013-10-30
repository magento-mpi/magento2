<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Controller;

/**
 * Controller context
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Context extends \Magento\Core\Controller\Varien\Action\Context
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
     * @param \Magento\Core\Model\Logger $logger
     * @param \Magento\App\RequestInterface $request
     * @param \Magento\App\ResponseInterface $response
     * @param \Magento\ObjectManager $objectManager
     * @param \Magento\App\FrontController $frontController
     * @param \Magento\View\LayoutInterface $layout
     * @param \Magento\Event\ManagerInterface $eventManager
     * @param bool $isRenderInherited
     * @param \Magento\Backend\Model\Session $session
     * @param \Magento\Backend\Helper\Data $helper
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
        \Magento\Core\Model\Logger $logger,
        \Magento\App\RequestInterface $request,
        \Magento\App\ResponseInterface $response,
        \Magento\ObjectManager $objectManager,
        \Magento\App\FrontController $frontController,
        \Magento\View\LayoutInterface $layout,
        \Magento\Event\ManagerInterface $eventManager,
        $isRenderInherited,
        \Magento\Backend\Model\Session $session,
        \Magento\Backend\Helper\Data $helper,
        \Magento\AuthorizationInterface $authorization,
        \Magento\Core\Model\Translate $translator,
        \Magento\Backend\Model\Auth $auth,
        \Magento\Backend\Model\Url $backendUrl,
        \Magento\Core\Model\LocaleInterface $locale,
        $canUseBaseUrl = false
    ) {
        parent::__construct($logger, $request, $response, $objectManager, $frontController, $layout, $eventManager, 
            $isRenderInherited
        );
        $this->_session = $session;
        $this->_helper = $helper;
        $this->_authorization = $authorization;
        $this->_translator = $translator;
        $this->_auth = $auth;
        $this->_backendUrl = $backendUrl;
        $this->_locale = $locale;
    }

    /**
     * @return \Magento\Backend\Helper\Data
     */
    public function getHelper()
    {
        return $this->_helper;
    }

    /**
     * @return \Magento\Backend\Model\Session
     */
    public function getSession()
    {
        return $this->_session;
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
