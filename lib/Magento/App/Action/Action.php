<?php
/**
 * Default implementation of application action controller
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App\Action;

use Magento\App\RequestInterface;

class Action extends \Magento\App\Action\AbstractAction
{
    const FLAG_NO_DISPATCH              = 'no-dispatch';
    const FLAG_NO_POST_DISPATCH         = 'no-postDispatch';
    const FLAG_NO_DISPATCH_BLOCK_EVENT  = 'no-beforeGenerateLayoutBlocksDispatch';

    const PARAM_NAME_SUCCESS_URL        = 'success_url';
    const PARAM_NAME_ERROR_URL          = 'error_url';
    const PARAM_NAME_BASE64_URL         = 'r64';
    const PARAM_NAME_URL_ENCODED        = 'uenc';

    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * Namespace for session.
     * Should be defined for proper working session.
     *
     * @var string
     */
    protected $_sessionNamespace;

    /**
     * @var \Magento\Event\ManagerInterface
     */
    protected $_eventManager;

    /**
     * @var \Magento\App\ActionFlag
     */
    protected $_actionFlag;

    /**
     * @var \Magento\HTTP\Url
     */
    protected $_appUrl;

    /**
     * @var \Magento\App\Request\Redirect
     */
    protected $_redirect;

    /**
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\View\Action\LayoutServiceInterface
     */
    protected $_layoutServices;

    /**
     * @var \Magento\Core\Model\Session\AbstractSession
     */
    protected $_session;

    /**
     * @var \Magento\Core\Model\Url
     */
    protected $_url;

    /**
     * @param Context $context
     */
    public function __construct(\Magento\App\Action\Context $context)
    {
        parent::__construct($context->getRequest(), $context->getResponse());

        $context->getFrontController()->setAction($this);
        $this->_objectManager     = $context->getObjectManager();
        $this->_eventManager      = $context->getEventManager();
        $this->_storeManager      = $context->getStoreManager();
        $this->_session           = $context->getSession();
        $this->_url               = $context->getUrl();
        $this->_actionFlag        = $context->getActionFlag();
        $this->_appUrl            = $context->getAppUrl();
        $this->_redirect          = $context->getRedirect();
        $this->_layoutServices    = $context->getLayoutServices();
    }

    /**
     * Retrieve flag value
     *
     * @param   string $action
     * @param   string $flag
     * @return  bool
     */
    public function getFlag($action, $flag = '') // Leave
    {
        return $this->_actionFlag->get($action, $flag);
    }

    /**
     * Setting flag value
     *
     * @param   string $action
     * @param   string $flag
     * @param   string $value
     * @return  \Magento\App\ActionInterface
     */
    public function setFlag($action, $flag, $value)  // Leave
    {
        $this->_actionFlag->set($action, $flag, $value);
        return $this;
    }

    /**
     * @param RequestInterface $request
     * @return mixed
     * @throws NotFoundException
     */
    public function dispatch(RequestInterface $request)
    {
        $this->_request = $request;
        $profilerKey = 'CONTROLLER_ACTION:' . $this->getFullActionName();
        $this->_eventManager->dispatch('controller_action_predispatch', array('controller_action' => $this));
        $this->_eventManager->dispatch(
            'controller_action_predispatch_' . $request->getRouteName(), array('controller_action' => $this)
        );
        $this->_eventManager->dispatch(
            'controller_action_predispatch_' . $request->getActionName() . 'Action', array('controller_action' => $this)
        );
        \Magento\Profiler::start($profilerKey);

        if ($request->isDispatched() && !$this->getFlag('', self::FLAG_NO_DISPATCH)) {
            \Magento\Profiler::start('action_body');
            $actionMethodName = $request->getActionName() . 'Action';
            $this->$actionMethodName();
            \Magento\Profiler::start('postdispatch');
            if (!$this->getFlag('', \Magento\App\Action\Action::FLAG_NO_POST_DISPATCH)) {
                $this->_eventManager->dispatch(
                    'controller_action_postdispatch_' . $this->getFullActionName(), array('controller_action' => $this)
                );
                $this->_eventManager->dispatch(
                    'controller_action_postdispatch_' . $request->getRouteName(), array('controller_action' => $this)
                );
                $this->_eventManager->dispatch('controller_action_postdispatch', array('controller_action' => $this));
            }
            \Magento\Profiler::stop('postdispatch');
            \Magento\Profiler::stop('action_body');
        }
        \Magento\Profiler::stop($profilerKey);
    }

    /**
     * Throw control to different action (control and module if was specified).
     *
     * @param string $action
     * @param string|null $controller
     * @param string|null $module
     * @param array|null $params
     */
    protected function _forward($action, $controller = null, $module = null, array $params = null) // Leave
    {
        $request = $this->getRequest();

        $request->initForward();

        if (isset($params)) {
            $request->setParams($params);
        }

        if (isset($controller)) {
            $request->setControllerName($controller);

            // Module should only be reset if controller has been specified
            if (isset($module)) {
                $request->setModuleName($module);
            }
        }

        $request->setActionName($action)
            ->setDispatched(false);
    }

    /**
     * Set redirect into response
     *
     * @param   string $path
     * @param   array $arguments
     * @return  \Magento\App\ActionInterface
     */
    protected function _redirect($path, $arguments = array()) // Inline/leave
    {
        if ($this->_session->getCookieShouldBeReceived()
            && $this->_url->getUseSession()
            && $this->_sessionNamespace != \Magento\Backend\App\AbstractAction::SESSION_NAMESPACE
        ) {
            $arguments += array('_query' => array(
                $this->_session->getSessionIdQueryParam() => $this->_session->getSessionId()
            ));
        }
        $this->getResponse()->setRedirect(
            $this->_url->getUrl($path, $arguments)
        );
        return $this;
    }

    /**
     * Redirect to success page
     *
     * @param string $defaultUrl
     * @return \Magento\App\ActionInterface
     */
    protected function _redirectSuccess($defaultUrl) // leave, inline?
    {
        $successUrl = $this->getRequest()->getParam(self::PARAM_NAME_SUCCESS_URL);
        if (empty($successUrl)) {
            $successUrl = $defaultUrl;
        }
        if (!$this->_appUrl->isInternal($successUrl)) {
            $successUrl = $this->_storeManager->getStore()->getBaseUrl();
        }
        $this->getResponse()->setRedirect($successUrl);
        return $this;
    }

    /**
     * Redirect to error page
     *
     * @param string $defaultUrl
     * @return  \Magento\App\ActionInterface
     */
    protected function _redirectError($defaultUrl) // extract
    {
        $errorUrl = $this->getRequest()->getParam(self::PARAM_NAME_ERROR_URL);
        if (empty($errorUrl)) {
            $errorUrl = $defaultUrl;
        }
        if (!$this->_appUrl->isInternal($errorUrl)) {
            $errorUrl = $this->_storeManager->getStore()->getBaseUrl();
        }
        $this->getResponse()->setRedirect($errorUrl);
        return $this;
    }
}
