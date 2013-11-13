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
     * @var \Magento\Core\Model\Store\Config
     */
    protected $_storeConfig;

    /**
     * @param \Magento\Core\Model\Session $session
     * @param \Magento\Core\Model\Cookie $cookie
     * @param \Magento\Core\Model\Url $url
     * @param \Magento\App\ActionFlag $flag
     * @param \Magento\Core\Model\Store\Config $storeConfig
     * @param string $sessionNamespace
     * @param array $cookieCheckActions
     */
    public function __construct(
        \Magento\App\ActionFlag $flag,
        \Magento\Core\Model\Session $session,
        \Magento\Core\Model\Cookie $cookie,
        \Magento\Core\Model\Url $url,
        \Magento\Core\Model\Store\Config $storeConfig,
        $sessionNamespace = '',
        array $cookieCheckActions = array()
    ) {
        $this->_session = $session;
        $this->_cookie = $cookie;
        $this->_cookieCheckActions = $cookieCheckActions;
        $this->_url = $url;
        $this->_sessionNamespace = $sessionNamespace;
        $this->_flag = $flag;
        $this->_storeConfig = $storeConfig;
    }

    /**
     * @param array $arguments
     * @param \Magento\Code\Plugin\InvocationChain $invocationChain
     * @return array
     */
    public function aroundDispatch(array $arguments = array(), \Magento\Code\Plugin\InvocationChain $invocationChain)
    {
        $request = $arguments[0];
        $checkCookie = in_array($request->getActionName(), $this->_cookieCheckActions)
            && !$request->getParam('nocookie', false);

        $cookies = $this->_cookie->get();
        /** @var $session \Magento\Core\Model\Session */
        $session = $this->_session->start();

        if (empty($cookies)) {
            if ($session->getCookieShouldBeReceived()) {
                $session->unsCookieShouldBeReceived();
                $session->setSkipSessionIdFlag(true);
                if ($this->_storeConfig->getConfig('web/browser_capabilities/cookies')) {
                    $this->_forward($request);
                    return null;
                }
            } elseif ($checkCookie) {
                if (isset($_GET[$session->getSessionIdQueryParam()])
                    && $this->_url->getUseSession()
                    && $this->_sessionNamespace != \Magento\Backend\App\AbstractAction::SESSION_NAMESPACE
                ) {
                    $session->setCookieShouldBeReceived(true);
                } else {
                    $this->_forward($request);
                    return null;
                }
            }
        }
        return $invocationChain->proceed($arguments);
    }

    /**
     * Forward to noCookies action
     *
     * @param \Magento\App\RequestInterface $request
     * @return \Magento\App\RequestInterface
     */
    protected function _forward(\Magento\App\RequestInterface $request)
    {
        $request->initForwared();
        $request->setActionName('noCookies');
        $request->setControllerName('index');
        $request->setModuleName('core');
        $request->setDispatched(false);
        return $request;
    }
}
