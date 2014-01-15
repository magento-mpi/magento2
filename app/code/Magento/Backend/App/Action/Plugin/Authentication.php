<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\App\Action\Plugin;


class Authentication
{
    /**
     * @var \Magento\Backend\Model\Auth
     */
    protected $_auth;

    /**
     * @var array
     */
    protected $_openActions = array(
        'forgotpassword',
        'resetpassword',
        'resetpasswordpost',
        'logout',
        'refresh' // captcha refresh
    );

    /**
     * @var \Magento\Backend\Model\Url
     */
    protected $_url;

    /**
     * @var \Magento\App\ResponseInterface
     */
    protected $_response;

    /**
     * @var \Magento\App\ActionFlag
     */
    protected $_actionFlag;

    /**
     * @var \Magento\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @param \Magento\Backend\Model\Auth $auth
     * @param \Magento\Backend\Model\Url $url
     * @param \Magento\App\ResponseInterface $response
     * @param \Magento\App\ActionFlag $actionFlag
     * @param \Magento\Message\ManagerInterface $messageManager
     */
    public function __construct(
        \Magento\Backend\Model\Auth $auth,
        \Magento\Backend\Model\Url $url,
        \Magento\App\ResponseInterface $response,
        \Magento\App\ActionFlag $actionFlag,
        \Magento\Message\ManagerInterface $messageManager
    ) {
        $this->_auth = $auth;
        $this->_url = $url;
        $this->_response = $response;
        $this->_actionFlag = $actionFlag;
        $this->messageManager = $messageManager;
    }

    /**
     * @param array $arguments
     * @param \Magento\Code\Plugin\InvocationChain $invocationChain
     * @return mixed
     */
    public function aroundDispatch(array $arguments, \Magento\Code\Plugin\InvocationChain $invocationChain)
    {
        $request = $arguments[0];
        $requestedActionName = $request->getActionName();
        if (in_array($requestedActionName, $this->_openActions)) {
            $request->setDispatched(true);
        } else {
            if ($this->_auth->getUser()) {
                $this->_auth->getUser()->reload();
            }
            if (!$this->_auth->isLoggedIn()) {
                $this->_processNotLoggedInUser($request);
            }
        }
        $this->_auth->getAuthStorage()->refreshAcl();
        return $invocationChain->proceed($arguments);
    }

    /**
     * Process not logged in user data
     *
     * @param \Magento\App\RequestInterface $request
     */
    protected function _processNotLoggedInUser(\Magento\App\RequestInterface $request)
    {
        $isRedirectNeeded = false;
        if ($request->getPost('login') && $this->_performLogin($request)) {
            $isRedirectNeeded = $this->_redirectIfNeededAfterLogin($request);
        }
        if (!$isRedirectNeeded && !$request->getParam('forwarded')) {
            if ($request->getParam('isIframe')) {
                $request->setParam('forwarded', true)
                    ->setRouteName('adminhtml')
                    ->setControllerName('auth')
                    ->setActionName('deniedIframe')
                    ->setDispatched(false);
            } elseif ($request->getParam('isAjax')) {
                $request->setParam('forwarded', true)
                    ->setRouteName('adminhtml')
                    ->setControllerName('auth')
                    ->setActionName('deniedJson')
                    ->setDispatched(false);
            } else {
                $request->setParam('forwarded', true)
                    ->setRouteName('adminhtml')
                    ->setControllerName('auth')
                    ->setActionName('login')
                    ->setDispatched(false);
            }
        }
    }

    /**
     * Performs login, if user submitted login form
     *
     * @param \Magento\App\RequestInterface $request
     * @return bool
     */
    protected function _performLogin(\Magento\App\RequestInterface $request)
    {
        $outputValue = true;
        $postLogin  = $request->getPost('login');
        $username   = isset($postLogin['username']) ? $postLogin['username'] : '';
        $password   = isset($postLogin['password']) ? $postLogin['password'] : '';
        $request->setPost('login', null);

        try {
            $this->_auth->login($username, $password);
        } catch (\Magento\Backend\Model\Auth\Exception $e) {
            if (!$request->getParam('messageSent')) {
                $this->messageManager->addError($e->getMessage());
                $request->setParam('messageSent', true);
                $outputValue = false;
            }
        }
        return $outputValue;
    }

    /**
     * Checks, whether Magento requires redirection after successful admin login, and redirects user, if needed
     *
     * @param \Magento\App\RequestInterface $request
     * @return bool
     */
    protected function _redirectIfNeededAfterLogin(\Magento\App\RequestInterface $request)
    {
        $requestUri = null;

        // Checks, whether secret key is required for admin access or request uri is explicitly set
        if ($this->_url->useSecretKey()) {
            $requestUri = $this->_url->getUrl('*/*/*', array('_current' => true));
        } elseif ($request) {
            $requestUri = $request->getRequestUri();
        }

        if (!$requestUri) {
            return false;
        }

        $this->_response->setRedirect($requestUri);
        $this->_actionFlag->set('', \Magento\App\Action\Action::FLAG_NO_DISPATCH, true);
        return true;
    }
}
