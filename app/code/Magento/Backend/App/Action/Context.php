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
 * Backend Controller context
 */
class Context extends \Magento\Framework\App\Action\Context
{
    /**
     * @var \Magento\Framework\AuthorizationInterface
     */
    protected $_authorization;

    /**
     * @var \Magento\Backend\Model\Auth
     */
    protected $_auth;

    /**
     * @var \Magento\Backend\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Magento\Backend\Model\UrlInterface
     */
    protected $_backendUrl;

    /**
     * @var \Magento\Core\App\Action\FormKeyValidator
     */
    protected $_formKeyValidator;

    /**
     * @var \Magento\Framework\App\Action\Title
     */
    protected $_title;

    /**
     * @var bool
     */
    protected $_canUseBaseUrl;

    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $_session;

    /**
     * @var \Magento\Locale\ResolverInterface
     */
    protected $_localeResolver;

    /**
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\App\ResponseInterface $response
     * @param \Magento\Framework\ObjectManager $objectManager
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\UrlInterface $url
     * @param \Magento\Framework\App\Response\RedirectInterface $redirect
     * @param \Magento\Framework\App\ActionFlag $actionFlag
     * @param \Magento\Framework\App\ViewInterface $view
     * @param \Magento\Message\ManagerInterface $messageManager
     * @param \Magento\Backend\Model\Session $session
     * @param \Magento\Framework\AuthorizationInterface $authorization
     * @param \Magento\Backend\Model\Auth $auth
     * @param \Magento\Backend\Helper\Data $helper
     * @param \Magento\Backend\Model\UrlInterface $backendUrl
     * @param \Magento\Core\App\Action\FormKeyValidator $formKeyValidator
     * @param \Magento\Framework\App\Action\Title $title
     * @param \Magento\Locale\ResolverInterface $localeResolver
     * @param bool $canUseBaseUrl
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\App\ResponseInterface $response,
        \Magento\Framework\ObjectManager $objectManager,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\UrlInterface $url,
        \Magento\Framework\App\Response\RedirectInterface $redirect,
        \Magento\Framework\App\ActionFlag $actionFlag,
        \Magento\Framework\App\ViewInterface $view,
        \Magento\Message\ManagerInterface $messageManager,
        \Magento\Backend\Model\Session $session,
        \Magento\Framework\AuthorizationInterface $authorization,
        \Magento\Backend\Model\Auth $auth,
        \Magento\Backend\Helper\Data $helper,
        \Magento\Backend\Model\UrlInterface $backendUrl,
        \Magento\Core\App\Action\FormKeyValidator $formKeyValidator,
        \Magento\Framework\App\Action\Title $title,
        \Magento\Locale\ResolverInterface $localeResolver,
        $canUseBaseUrl = false
    ) {
        parent::__construct(
            $request,
            $response,
            $objectManager,
            $eventManager,
            $url,
            $redirect,
            $actionFlag,
            $view,
            $messageManager
        );

        $this->_session = $session;
        $this->_authorization = $authorization;
        $this->_auth = $auth;
        $this->_helper = $helper;
        $this->_backendUrl = $backendUrl;
        $this->_formKeyValidator = $formKeyValidator;
        $this->_title = $title;
        $this->_localeResolver = $localeResolver;
        $this->_canUseBaseUrl = $canUseBaseUrl;
    }

    /**
     * @return \Magento\Backend\Model\Auth
     */
    public function getAuth()
    {
        return $this->_auth;
    }

    /**
     * @return \Magento\Framework\AuthorizationInterface
     */
    public function getAuthorization()
    {
        return $this->_authorization;
    }

    /**
     * @return \Magento\Backend\Model\UrlInterface
     */
    public function getBackendUrl()
    {
        return $this->_backendUrl;
    }

    /**
     * @return boolean
     */
    public function getCanUseBaseUrl()
    {
        return $this->_canUseBaseUrl;
    }

    /**
     * @return \Magento\Core\App\Action\FormKeyValidator
     */
    public function getFormKeyValidator()
    {
        return $this->_formKeyValidator;
    }

    /**
     * @return \Magento\Backend\Helper\Data
     */
    public function getHelper()
    {
        return $this->_helper;
    }

    /**
     * @return \Magento\Locale\ResolverInterface
     */
    public function getLocaleResolver()
    {
        return $this->_localeResolver;
    }

    /**
     * @return \Magento\Framework\App\Action\Title
     */
    public function getTitle()
    {
        return $this->_title;
    }

    /**
     * @return \Magento\Backend\Model\Session
     */
    public function getSession()
    {
        return $this->_session;
    }
}
