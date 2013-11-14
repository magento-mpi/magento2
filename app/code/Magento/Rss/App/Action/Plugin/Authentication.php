<?php
/**
 * RSS Authentication plugin
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Rss\App\Action\Plugin;

class Authentication extends \Magento\Backend\App\Action\Plugin\Authentication
{
    /**
     * @var \Magento\Http\Authentication
     */
    protected $_httpAuthentication;

    /**
     * @var \Magento\Core\Model\Logger
     */
    protected $_logger;

    /**
     * @var \Magento\AuthorizationInterface
     */
    protected $_authorization;

    /**
     * @var array
     */
    protected $_aclResources = array(
        'authenticate' => 'Magento_Rss::rss',
        'catalog' => array(
            'notifystock' => 'Magento_Catalog::products',
            'review' => 'Magento_Review::reviews_all'
        ),
        'order' => 'Magento_Sales::sales_order'
    );

    /**
     * @param \Magento\Backend\Model\Auth $auth
     * @param \Magento\Backend\Model\Session $session
     * @param \Magento\Backend\Model\Url $url
     * @param \Magento\App\ResponseInterface $response
     * @param \Magento\App\ActionFlag $actionFlag
     * @param \Magento\Http\Authentication $httpAuthentication
     * @param \Magento\Logger $logger
     * @param \Magento\AuthorizationInterface $authorization
     */
    public function __construct(
        \Magento\Backend\Model\Auth $auth,
        \Magento\Backend\Model\Session $session,
        \Magento\Backend\Model\Url $url,
        \Magento\App\ResponseInterface $response,
        \Magento\App\ActionFlag $actionFlag,
        \Magento\Http\Authentication $httpAuthentication,
        \Magento\Logger $logger,
        \Magento\AuthorizationInterface $authorization
    ) {
        $this->_httpAuthentication = $httpAuthentication;
        $this->_logger = $logger;
        $this->_authorization = $authorization;
        parent::__construct($auth, $session, $url, $response, $actionFlag);
    }

    /**
     * Replace standard admin login form with HTTP Basic authentication
     *
     * @param array $arguments
     * @param \Magento\Code\Plugin\InvocationChain $invocationChain
     * @return mixed
     */
    public function aroundDispatch(array $arguments, \Magento\Code\Plugin\InvocationChain $invocationChain)
    {
        /** @var \Magento\App\RequestInterface $request */
        $request = $arguments[0];
        $resource = isset($this->_aclResources[$request->getControllerName()])
            ? (isset($this->_aclResources[$request->getControllerName()][$request->getActionName()])
                ? $this->_aclResources[$request->getControllerName()][$request->getActionName()]
                : $this->_aclResources[$request->getControllerName()])
            : null;
        if (!$resource) {
            return parent::aroundDispatch($arguments, $invocationChain);
        }

        $session = $this->_auth->getAuthStorage();

        // Try to login using HTTP-authentication
        if (!$session->isLoggedIn()) {
            list($login, $password) = $this->_httpAuthentication->getCredentials();
            try {
                $this->_auth->login($login, $password);
            } catch (\Magento\Backend\Model\Auth\Exception $e) {
                $this->_logger->logException($e);
            }
        }

        // Verify if logged in and authorized
        if (!$session->isLoggedIn() || !$this->_authorization->isAllowed($resource)) {
            $this->_httpAuthentication->setAuthenticationFailed('RSS Feeds');
            return null;
        }

        return parent::aroundDispatch($arguments, $invocationChain);
    }
}