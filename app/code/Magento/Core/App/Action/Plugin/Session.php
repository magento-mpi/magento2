<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\App\Action\Plugin;

class Session
{
    /**
     * @var \Magento\App\RequestInterface
     */
    protected $_request;

    /**
     * @var \Magento\Core\Model\Session
     */
    protected $_session;

    /**
     * @var \Magento\Core\Model\Cookie
     */
    protected $_cookie;

    /**
     * @var array
     */
    protected $_cookieCheckActions;

    /**
     * @var \Magento\Core\Model\Url
     */
    protected $_url;

    /**
     * @var string
     */
    protected $_sessionNamespace;

    /**
     * @var \Magento\App\ActionFlag
     */
    protected $_flag;

    /**
     * @param \Magento\App\RequestInterface $request
     * @param \Magento\Core\Model\Session $session
     * @param \Magento\Core\Model\Cookie $cookie
     * @param \Magento\Core\Model\Url $url
     * @param \Magento\App\ActionFlag $flag
     * @param string $sessionNamespace
     * @param array $cookieCheckActions
     */
    public function __construct(
        \Magento\App\RequestInterface $request,
        \Magento\Core\Model\Session $session,
        \Magento\Core\Model\Cookie $cookie,
        \Magento\Core\Model\Url $url,
        \Magento\App\ActionFlag $flag,
        $sessionNamespace = '',
        array $cookieCheckActions = array()
    ) {
        $this->_request = $request;
        $this->_session = $session;
        $this->_cookie = $cookie;
        $this->_cookieCheckActions = $cookieCheckActions;
        $this->_url = $url;
        $this->_sessionNamespace = $sessionNamespace;
        $this->_flag = $flag;
    }

    public function beforeDispatch(array $arguments = array())
    {
        $requestKey = $this->_request->getControllerModule() . '::'
            . $this->_request->getRequestedRouteName()
            . '/' . $this->_request->getRequestedControllerName()
            . '/' . $this->_request->getActionName();

        $checkCookie = in_array($requestKey, $this->_cookieCheckActions)
            && !$this->_request->getParam('nocookie', false);

        $cookies = $this->_cookie->get();
        /** @var $session \Magento\Core\Model\Session */
        $session = $this->_session->start();

        if (empty($cookies)) {
            if ($session->getCookieShouldBeReceived()) {
                $this->_flag->set('', \Magento\App\Action\Action::FLAG_NO_COOKIES_REDIRECT, true);
                $session->unsCookieShouldBeReceived();
                $session->setSkipSessionIdFlag(true);
            } elseif ($checkCookie) {
                if (isset($_GET[$session->getSessionIdQueryParam()])
                    && $this->_url->getUseSession()
                    && $this->_sessionNamespace != \Magento\Backend\App\AbstractAction::SESSION_NAMESPACE
                ) {
                    $session->setCookieShouldBeReceived(true);
                } else {
                    $this->_flag->get('', \Magento\App\Action\Action::FLAG_NO_COOKIES_REDIRECT, true);
                }
            }
        }
        return $arguments;
    }
}
