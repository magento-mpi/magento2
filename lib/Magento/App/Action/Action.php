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
     * Real module name (like 'Magento_Module')
     *
     * @var string
     */
    protected $_realModuleName;

    /**
     * Namespace for session.
     * Should be defined for proper working session.
     *
     * @var string
     */
    protected $_sessionNamespace;

    /**
     * Whether layout is loaded
     *
     * @see self::loadLayout()
     * @var bool
     */
    protected $_isLayoutLoaded = false;

    /**
     * @var \Magento\App\FrontController
     */
    protected $_frontController = null;

    /**
     * @var \Magento\View\LayoutInterface
     */
    protected $_layout;

    /**
     * @var \Magento\Event\ManagerInterface
     */
    protected $_eventManager;

    /**
     * @var \Magento\App\ActionFlag
     */
    protected $_flag;

    /** @var \Magento\Encryption\UrlCoder */
    protected $_urlCoder;

    /**
     * @var \Magento\HTTP\Url
     */
    protected $_appUrl;

    /** @var \Magento\App\Request\Redirect */
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
     * @param Context $context
     */
    public function __construct(\Magento\App\Action\Context $context)
    {
        parent::__construct($context->getRequest(), $context->getResponse());

        $context->getFrontController()->setAction($this);
        $this->_objectManager     = $context->getObjectManager();
        $this->_layout            = $context->getLayout();
        $this->_eventManager      = $context->getEventManager();
        $this->_configScope       = $context->getConfigScope();
        $this->_storeManager      = $context->getStoreManager();
        $this->_appState          = $context->getAppState();
        $this->_locale            = $context->getLocale();
        $this->_session           = $context->getSession();
        $this->_filesystem        = $context->getFilesystem();
        $this->_url               = $context->getUrl();
        $this->_translate         = $context->getTranslate();
        $this->_storeConfig       = $context->getStoreConfig();
        $this->_cookie            = $context->getCookie();
        $this->_app               = $context->getApp();
        $this->_helper            = $context->getHelper();
        $this->_flag              = $context->getFlag();
        $this->_urlCoder          = $context->getUrlCoder();
        $this->_appUrl            = $context->getHttpUrl();
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
        return $this->_flag->get($action, $flag);
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
        $this->_flag->set($action, $flag, $value);
        return $this;
    }

    /**
     * Retrieve current layout object
     *
     * @return \Magento\View\LayoutInterface
     */
    public function getLayout()  // TODO: Remove 12 usages
    {
        return $this->_layoutServices->getLayout();
    }

    /**
     * Load layout by handles(s)
     *
     * @param   string|null|bool $handles
     * @param   bool $generateBlocks
     * @param   bool $generateXml
     * @return  $this
     * @throws  \RuntimeException
     */
    public function loadLayout($handles = null, $generateBlocks = true, $generateXml = true)  //TODO fix 2 usages
    {
        $this->_layoutServices->loadLayout($handles, $generateBlocks, $generateXml);
        return $this;
    }

    /**
     * Retrieve the default layout handle name for the current action
     *
     * @return string
     */
    public function getDefaultLayoutHandle()  // Leave
    {
        return $this->_layoutServices->getDefaultLayoutHandle();
    }

    /**
     * Add layout handle by full controller action name
     *
     * @return \Magento\App\ActionInterface
     */
    public function addActionLayoutHandles()  // Leave
    {
        $this->_layoutServices->addActionLayoutHandles();
        return $this;
    }

    /**
     * Add layout updates handles associated with the action page
     *
     * @param array $parameters page parameters
     * @return bool
     */
    public function addPageLayoutHandles(array $parameters = array())  // Leave
    {
        return $this->_layoutServices->addPageLayoutHandles($parameters);
    }

    /**
     * Load layout updates
     *
     * @return $this
     */
    public function loadLayoutUpdates()  // Leave
    {
        //2 events

        // dispatch event for adding handles to layout update
        $this->_eventManager->dispatch(
            'controller_action_layout_load_before',
            array('action' => $this, 'layout' => $this->getLayout())
        );

        return $this->_layoutServices->loadLayoutUpdates();
    }

    /**
     * Generate layout xml
     *
     * @return $this
     */
    public function generateLayoutXml()  // Leave
    {
        $this->_layoutServices->generateLayoutXml();
        return $this;
    }

    /**
     * Generate layout blocks
     *
     * @return $this
     */
    public function generateLayoutBlocks()  //TODO fix 4 usages
    {
        // 1 event
        // dispatch event for adding xml layout elements
        if (!$this->_flag->get('', \Magento\App\Action\Action::FLAG_NO_DISPATCH_BLOCK_EVENT)) {
            $this->_eventManager->dispatch(
                'controller_action_layout_generate_blocks_before',
                array('action' => $this, 'layout' => $this->_layoutServices->getLayout())
            );
        }

        $this->_layoutServices->generateLayoutBlocks();

        // 4 events
        if (!$this->_flag->get('', \Magento\App\Action\Action::FLAG_NO_DISPATCH_BLOCK_EVENT)) {
            $this->_eventManager->dispatch(
                'controller_action_layout_generate_blocks_after',
                array('action' => $this, 'layout' => $this->_layoutServices->getLayout())
            );
        }

        return $this;
    }

    /**
     * Rendering layout
     *
     * @param   string $output
     * @return  \Magento\App\ActionInterface
     */
    public function renderLayout($output = '')  //TODO fix 3 usages
    {
        $this->_layoutServices->renderLayout($output);
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
