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

class Action extends \Magento\App\Action\AbstractAction
{
    const FLAG_NO_CHECK_INSTALLATION    = 'no-install-check';
    const FLAG_NO_DISPATCH              = 'no-dispatch';
    const FLAG_NO_PRE_DISPATCH          = 'no-preDispatch';
    const FLAG_NO_POST_DISPATCH         = 'no-postDispatch';
    const FLAG_NO_DISPATCH_BLOCK_EVENT  = 'no-beforeGenerateLayoutBlocksDispatch';
    const FLAG_NO_COOKIES_REDIRECT      = 'no-cookies-redirect';

    const PARAM_NAME_SUCCESS_URL        = 'success_url';
    const PARAM_NAME_ERROR_URL          = 'error_url';
    const PARAM_NAME_REFERER_URL        = 'referer_url';
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
     * Title parts to be rendered in the page head title
     *
     * @see self::_title()
     * @var array
     */
    protected $_titles = array();

    /**
     * Whether the default title should be removed
     *
     * @see self::_title()
     * @var bool
     */
    protected $_removeDefaultTitle = false;

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
     * Should inherited page be rendered
     *
     * @var bool
     */
    protected $_isRenderInherited;

    /**
     * @var \Magento\HTTP\Authentication
     */
    protected $authentication;

    /**
     * @var \Magento\App\ActionFlag
     */
    protected $_flag;

    /**
     * @param Context $context
     */
    public function __construct(\Magento\App\Action\Context $context)
    {
        parent::__construct($context->getRequest(), $context->getResponse());

        $this->_objectManager     = $context->getObjectManager();
        $this->_frontController   = $context->getFrontController();
        $this->_layout            = $context->getLayout();
        $this->_eventManager      = $context->getEventManager();
        $this->_isRenderInherited = $context->isRenderInherited();
        $this->_frontController->setAction($this);
        $this->authentication     = $context->getAuthentication();
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
    public function getLayout()  // Leave
    {
        $this->_layout->setArea($this->_configScope->getCurrentScope());
        return $this->_layout;
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
    public function loadLayout($handles = null, $generateBlocks = true, $generateXml = true)  // Leave
    {
        if ($this->_isLayoutLoaded) {
            throw new \RuntimeException('Layout must be loaded only once.');
        }
        // if handles were specified in arguments load them first
        if (false !== $handles && '' !== $handles) {
            $this->getLayout()->getUpdate()->addHandle($handles ? $handles : 'default');
        }

        // add default layout handles for this action
        $this->addActionLayoutHandles();

        $this->loadLayoutUpdates();

        if (!$generateXml) {
            return $this;
        }
        $this->generateLayoutXml();

        if (!$generateBlocks) {
            return $this;
        }
        $this->generateLayoutBlocks();
        $this->_isLayoutLoaded = true;

        return $this;
    }

    /**
     * Retrieve the default layout handle name for the current action
     *
     * @return string
     */
    public function getDefaultLayoutHandle()  // Leave
    {
        return strtolower($this->getFullActionName());
    }

    /**
     * Add layout handle by full controller action name
     *
     * @return \Magento\App\ActionInterface
     */
    public function addActionLayoutHandles()  // Leave
    {
        if (!$this->_isRenderInherited || !$this->addPageLayoutHandles()) {
            $this->getLayout()->getUpdate()->addHandle($this->getDefaultLayoutHandle());
        }
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
        $handle = $this->getDefaultLayoutHandle();
        $pageHandles = array($handle);
        foreach ($parameters as $key => $value) {
            $pageHandles[] = $handle . '_' . $key . '_' . $value;
        }
        // Do not sort array going into add page handles. Ensure default layout handle is added first.
        return $this->getLayout()->getUpdate()->addPageHandles($pageHandles);
    }

    /**
     * Load layout updates
     *
     * @return $this
     */
    public function loadLayoutUpdates()  // Leave
    {
        \Magento\Profiler::start('LAYOUT');

        // dispatch event for adding handles to layout update
        $this->_eventManager->dispatch(
            'controller_action_layout_load_before',
            array('action' => $this, 'layout' => $this->getLayout())
        );

        // load layout updates by specified handles
        \Magento\Profiler::start('layout_load');
        $this->getLayout()->getUpdate()->load();
        \Magento\Profiler::stop('layout_load');

        \Magento\Profiler::stop('LAYOUT');
        return $this;
    }

    /**
     * Generate layout xml
     *
     * @return $this
     */
    public function generateLayoutXml()  // Leave
    {
        \Magento\Profiler::start('LAYOUT');

        // dispatch event for adding text layouts
        if (!$this->getFlag('', self::FLAG_NO_DISPATCH_BLOCK_EVENT)) {
            $this->_eventManager->dispatch(
                'controller_action_layout_generate_xml_before',
                array('action' => $this, 'layout' => $this->getLayout())
            );
        }

        // generate xml from collected text updates
        \Magento\Profiler::start('layout_generate_xml');
        $this->getLayout()->generateXml();
        \Magento\Profiler::stop('layout_generate_xml');

        \Magento\Profiler::stop('LAYOUT');
        return $this;
    }

    /**
     * Generate layout blocks
     *
     * @return $this
     */
    public function generateLayoutBlocks()  // Leave
    {
        \Magento\Profiler::start('LAYOUT');

        // dispatch event for adding xml layout elements
        if (!$this->getFlag('', self::FLAG_NO_DISPATCH_BLOCK_EVENT)) {
            $this->_eventManager->dispatch(
                'controller_action_layout_generate_blocks_before',
                array('action' => $this, 'layout' => $this->getLayout())
            );
        }

        // generate blocks from xml layout
        \Magento\Profiler::start('layout_generate_blocks');
        $this->getLayout()->generateElements();
        \Magento\Profiler::stop('layout_generate_blocks');

        if (!$this->getFlag('', self::FLAG_NO_DISPATCH_BLOCK_EVENT)) {
            $this->_eventManager->dispatch(
                'controller_action_layout_generate_blocks_after',
                array('action' => $this, 'layout' => $this->getLayout())
            );
        }

        \Magento\Profiler::stop('LAYOUT');
        return $this;
    }

    /**
     * Rendering layout
     *
     * @param   string $output
     * @return  \Magento\App\ActionInterface
     */
    public function renderLayout($output = '')  // Leave
    {
        if ($this->getFlag('', 'no-renderLayout')) {
            return;
        }

        \Magento\Profiler::start('LAYOUT');

        $this->_renderTitles();

        \Magento\Profiler::start('layout_render');

        if ('' !== $output) {
            $this->getLayout()->addOutputElement($output);
        }

        $this->_eventManager->dispatch('controller_action_layout_render_before');
        $this->_eventManager->dispatch('controller_action_layout_render_before_' . $this->getFullActionName());

        $output = $this->getLayout()->getOutput();
        $this->_translate->processResponseBody($output);
        $this->getResponse()->appendBody($output);
        \Magento\Profiler::stop('layout_render');

        \Magento\Profiler::stop('LAYOUT');
        return $this;
    }

    /**
     * Dispatch action
     *
     * @param string $action
     */
    public function dispatch($action)   // Leave
    {
        $this->getRequest()->setDispatched(true);
        try {
            $actionMethodName = $this->getActionMethodName($action);
            if (!method_exists($this, $actionMethodName)) {
                $actionMethodName = 'norouteAction';
            }

            $profilerKey = 'CONTROLLER_ACTION:' . $this->getFullActionName();
            \Magento\Profiler::start($profilerKey);

            \Magento\Profiler::start('predispatch');
            $this->preDispatch();
            \Magento\Profiler::stop('predispatch');

            if ($this->getRequest()->isDispatched()) {
                /**
                 * preDispatch() didn't change the action, so we can continue
                 */
                if (!$this->getFlag('', self::FLAG_NO_DISPATCH)) {
                    \Magento\Profiler::start('action_body');
                    $this->$actionMethodName();
                    \Magento\Profiler::stop('action_body');

                    \Magento\Profiler::start('postdispatch');
                    if (!$this->getFlag('', self::FLAG_NO_POST_DISPATCH)) {
                        $this->_eventManager->dispatch(
                            'controller_action_postdispatch_' . $this->getFullActionName(),
                            array('controller_action' => $this)
                        );
                        $this->_eventManager->dispatch(
                            'controller_action_postdispatch_' . $this->getRequest()->getRouteName(),
                            array('controller_action' => $this)
                        );
                        $this->_eventManager->dispatch('controller_action_postdispatch', array('controller_action' => $this));
                    }
                    \Magento\Profiler::stop('postdispatch');
                }
            }

            \Magento\Profiler::stop($profilerKey);
        } catch (\Magento\App\Action\Exception $e) {
            // set prepared flags
            foreach ($e->getResultFlags() as $flagData) {
                list($action, $flag, $value) = $flagData;
                $this->setFlag($action, $flag, $value);
            }
            // call forward, redirect or an action
            list($method, $parameters) = $e->getResultCallback();
            switch ($method) {
                case \Magento\App\Action\Exception::RESULT_REDIRECT:
                    list($path, $arguments) = $parameters;
                    $this->_redirect($path, $arguments);
                    break;
                case \Magento\App\Action\Exception::RESULT_FORWARD:
                    list($action, $controller, $module, $params) = $parameters;
                    $this->_forward($action, $controller, $module, $params);
                    break;
                default:
                    $actionMethodName = $this->getActionMethodName($method);
                    $this->getRequest()->setActionName($method);
                    $this->$actionMethodName($method);
                    break;
            }
        }
    }

    /**
     * Retrieve action method name
     *
     * @param string $action
     * @return string
     */
    public function getActionMethodName($action)  // Leave
    {
        return $action . 'Action';
    }

    /**
     * Dispatch event before action
     *
     * @return null
     */
    public function preDispatch() // Remove???
    {
        if ($this->getFlag('', self::FLAG_NO_COOKIES_REDIRECT)
            && $this->_storeConfig->getConfig('web/browser_capabilities/cookies')
        ) {
            $this->_forward('noCookies', 'index', 'core');
            return;
        }

        if ($this->getFlag('', self::FLAG_NO_PRE_DISPATCH)) {
            return;
        }

        $this->_firePreDispatchEvents();
    }

    /**
     * Fire predispatch events, execute extra logic after predispatch
     */
    protected function _firePreDispatchEvents() // Inline
    {
        $this->_eventManager->dispatch('controller_action_predispatch', array('controller_action' => $this));
        $this->_eventManager->dispatch('controller_action_predispatch_' . $this->getRequest()->getRouteName(),
            array('controller_action' => $this));
        $this->_eventManager->dispatch('controller_action_predispatch_' . $this->getFullActionName(),
            array('controller_action' => $this));
    }

    /**
     * No route action
     *
     * @param null $coreRoute
     */
    public function norouteAction($coreRoute = null) // Extract
    {
        $status = $this->getRequest()->getParam('__status__');
        if (!$status instanceof \Magento\Object) {
            $status = new \Magento\Object();
        }

        $this->_eventManager->dispatch('controller_action_noroute', array('action' => $this, 'status' => $status));

        if ($status->getLoaded() !== true
            || $status->getForwarded() === true
            || !is_null($coreRoute)
        ) {
            $this->loadLayout(array('default', 'noRoute'));
            $this->renderLayout();
        } else {
            $status->setForwarded(true);
            $this->_forward(
                $status->getForwardAction(),
                $status->getForwardController(),
                $status->getForwardModule(),
                array('__status__' => $status));
        }
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
        if (!$this->_isUrlInternal($successUrl)) {
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
        if (!$this->_isUrlInternal($errorUrl)) {
            $errorUrl = $this->_storeManager->getStore()->getBaseUrl();
        }
        $this->getResponse()->setRedirect($errorUrl);
        return $this;
    }

    /**
     * Set referer url for redirect in response
     *
     * @param   string $defaultUrl
     * @return  \Magento\App\ActionInterface
     */
    protected function _redirectReferer($defaultUrl=null) // extract
    {

        $refererUrl = $this->_getRefererUrl();
        if (empty($refererUrl)) {
            $refererUrl = empty($defaultUrl)
                ? $this->_storeManager->getBaseUrl()
                : $defaultUrl;
        }

        $this->getResponse()->setRedirect($refererUrl);
        return $this;
    }

    /**
     * Identify referer url via all accepted methods (HTTP_REFERER, regular or base64-encoded request param)
     *
     * @return string
     */
    protected function _getRefererUrl() // extract
    {
        $refererUrl = $this->getRequest()->getServer('HTTP_REFERER');
        $url = $this->getRequest()->getParam(self::PARAM_NAME_REFERER_URL);
        if ($url) {
            $refererUrl = $url;
        }
        $url = $this->getRequest()->getParam(self::PARAM_NAME_BASE64_URL);
        if ($url) {
            $refererUrl = $this->_urlCoder->decode($url);
        }
        $url = $this->getRequest()->getParam(self::PARAM_NAME_URL_ENCODED);
        if ($url) {
            $refererUrl = $this->_urlCoder->decode($url);
        }

        if (!$this->_isUrlInternal($refererUrl)) {
            $refererUrl = $this->_storeManager->getStore()->getBaseUrl();
        }
        return $refererUrl;
    }

    /**
     * Check url to be used as internal
     *
     * @param   string $url
     * @return  bool
     */
    protected function _isUrlInternal($url) // extract
    {
        if (strpos($url, 'http') !== false) {
            /**
             * Url must start from base secure or base unsecure url
             */
            /** @var $store \Magento\Core\Model\StoreManagerInterface */
            $store = $this->_storeManager->getStore();
            if ((strpos($url, $store->getBaseUrl()) === 0)
                || (strpos($url, $store->getBaseUrl(\Magento\Core\Model\Store::URL_TYPE_LINK, true)) === 0)
            ) {
                return true;
            }
        }
        return false;
    }

    /**
     * Validate Form Key
     *
     * @return bool
     */
    protected function _validateFormKey() // Extract ?
    {
        if (!($formKey = $this->getRequest()->getParam('form_key', null))
            || $formKey != $this->_session->getFormKey()
        ) {
            return false;
        }
        return true;
    }

    /**
     * Add an extra title to the end
     *
     * Usage examples:
     * $this->_title('foo')->_title('bar');
     * => bar / foo / <default title>
     *
     * @see self::_renderTitles()
     * @param string $text
     * @return \Magento\App\ActionInterface
     */
    protected function _title($text)
    {
        $this->_titles[] = $text;
        return $this;
    } // Leave

    /**
     * Prepare titles in the 'head' layout block
     * Supposed to work only in actions where layout is rendered
     * Falls back to the default logic if there are no titles eventually
     *
     * @see self::loadLayout()
     * @see self::renderLayout()
     */
    protected function _renderTitles() // leave/ KILLLLL
    {
        if ($this->_isLayoutLoaded && $this->_titles) {
            $titleBlock = $this->getLayout()->getBlock('head');
            if ($titleBlock) {
                if (!$this->_removeDefaultTitle) {
                    $title = trim($titleBlock->getTitle());
                    if ($title) {
                        array_unshift($this->_titles, $title);
                    }
                }
                $titleBlock->setTitle(array_reverse($this->_titles));
            }
        }
    }

    /**
     * Convert dates in array from localized to internal format
     *
     * @param   array $array
     * @param   array $dateFields
     * @return  array
     */
    protected function _filterDates($array, $dateFields)  // LKILL
    {
        if (empty($dateFields)) {
            return $array;
        }
        $filterInput = new \Zend_Filter_LocalizedToNormalized(array(
            'date_format' => $this->_locale->getDateFormat(\Magento\Core\Model\LocaleInterface::FORMAT_TYPE_SHORT)
        ));
        $filterInternal = new \Zend_Filter_NormalizedToLocalized(array(
            'date_format' => \Magento\Stdlib\DateTime::DATE_INTERNAL_FORMAT
        ));

        foreach ($dateFields as $dateField) {
            if (array_key_exists($dateField, $array) && !empty($dateField)) {
                $array[$dateField] = $filterInput->filter($array[$dateField]);
                $array[$dateField] = $filterInternal->filter($array[$dateField]);
            }
        }
        return $array;
    }

    /**
     * Convert dates with time in array from localized to internal format
     *
     * @param   array $array
     * @param   array $dateFields
     * @return  array
     */
    protected function _filterDateTime($array, $dateFields) // LKill
    {
        if (empty($dateFields)) {
            return $array;
        }
        $filterInput = new \Zend_Filter_LocalizedToNormalized(array(
            'date_format' => $this->_locale->getDateTimeFormat(\Magento\Core\Model\LocaleInterface::FORMAT_TYPE_SHORT)
        ));
        $filterInternal = new \Zend_Filter_NormalizedToLocalized(array(
            'date_format' => \Magento\Stdlib\DateTime::DATETIME_INTERNAL_FORMAT
        ));

        foreach ($dateFields as $dateField) {
            if (array_key_exists($dateField, $array) && !empty($dateField)) {
                $array[$dateField] = $filterInput->filter($array[$dateField]);
                $array[$dateField] = $filterInternal->filter($array[$dateField]);
            }
        }
        return $array;
    }

}
