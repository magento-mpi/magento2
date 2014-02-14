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
use Magento\App\ResponseInterface;

class Action extends \Magento\App\Action\AbstractAction
{
    const FLAG_NO_DISPATCH              = 'no-dispatch';
    const FLAG_NO_POST_DISPATCH         = 'no-postDispatch';
    const FLAG_NO_DISPATCH_BLOCK_EVENT  = 'no-beforeGenerateLayoutBlocksDispatch';

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
     * @var \Magento\App\Response\RedirectInterface
     */
    protected $_redirect;

    /**
     * @var \Magento\App\ViewInterface
     */
    protected $_view;

    /**
     * @var \Magento\UrlInterface
     */
    protected $_url;

    /**
     * @var \Magento\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @param Context $context
     */
    public function __construct(\Magento\App\Action\Context $context)
    {
        parent::__construct($context->getRequest(), $context->getResponse());
        $this->_objectManager     = $context->getObjectManager();
        $this->_eventManager      = $context->getEventManager();
        $this->_url               = $context->getUrl();
        $this->_actionFlag        = $context->getActionFlag();
        $this->_redirect          = $context->getRedirect();
        $this->_view              = $context->getView();
        $this->messageManager     = $context->getMessageManager();
    }

    /**
     * Dispatch request
     *
     * @param RequestInterface $request
     * @return ResponseInterface
     * @throws NotFoundException
     */
    public function dispatch(RequestInterface $request)
    {
        $this->_request = $request;
        $profilerKey = 'CONTROLLER_ACTION:' . $request->getFullActionName();
        $eventParameters = array('controller_action' => $this, 'request' => $request);
        $this->_eventManager->dispatch('controller_action_predispatch', $eventParameters);
        $this->_eventManager->dispatch('controller_action_predispatch_' . $request->getRouteName(), $eventParameters);
        $this->_eventManager->dispatch(
            'controller_action_predispatch_' . $request->getFullActionName(),
            $eventParameters
        );
        \Magento\Profiler::start($profilerKey);

        if ($request->isDispatched() && !$this->_actionFlag->get('', self::FLAG_NO_DISPATCH)) {
            \Magento\Profiler::start('action_body');
            $actionMethodName = $request->getActionName() . 'Action';
            $this->$actionMethodName();
            \Magento\Profiler::start('postdispatch');
            if (!$this->_actionFlag->get('', \Magento\App\Action\Action::FLAG_NO_POST_DISPATCH)) {
                $this->_eventManager->dispatch(
                    'controller_action_postdispatch_' . $request->getFullActionName(),
                    $eventParameters
                );
                $this->_eventManager->dispatch(
                    'controller_action_postdispatch_' . $request->getRouteName(), $eventParameters
                );
                $this->_eventManager->dispatch('controller_action_postdispatch', $eventParameters);
            }
            \Magento\Profiler::stop('postdispatch');
            \Magento\Profiler::stop('action_body');
        }
        \Magento\Profiler::stop($profilerKey);
        return $this->_response;
    }

    /**
     * Throw control to different action (control and module if was specified).
     *
     * @param string $action
     * @param string|null $controller
     * @param string|null $module
     * @param array|null $params
     * @return void
     */
    protected function _forward($action, $controller = null, $module = null, array $params = null)
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

        $request->setActionName($action);
        $request->setDispatched(false);
    }

    /**
     * Set redirect into response
     *
     * @param   string $path
     * @param   array $arguments
     * @return  ResponseInterface
     */
    protected function _redirect($path, $arguments = array())
    {
        $this->_redirect->redirect($this->getResponse(), $path, $arguments);
        return $this->getResponse();
    }
}
