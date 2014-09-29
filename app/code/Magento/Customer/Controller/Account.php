<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Controller;

use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session;
use Magento\Framework\App\RequestInterface;

/**
 * Customer account controller
 *
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Account extends \Magento\Framework\App\Action\Action
{
    /**
     * List of actions that are allowed for not authorized users
     *
     * @var string[]
     */
    protected $openActions = array(
        'create',
        'login',
        'logoutsuccess',
        'forgotpassword',
        'forgotpasswordpost',
        'resetpassword',
        'resetpasswordpost',
        'confirm',
        'confirmation',
        'createpassword',
        'createpost',
        'loginpost'
    );

    /** @var Session */
    protected $session;

    /**
     * @param Context $context
     * @param Session $customerSession
     */
    public function __construct(
        Context $context,
        Session $customerSession
    ) {
        $this->session = $customerSession;
        parent::__construct($context);
    }

    /**
     * Retrieve customer session model object
     *
     * @return Session
     */
    protected function _getSession()
    {
        return $this->session;
    }

    /**
     * Get list of actions that are allowed for not authorized users
     *
     * @return string[]
     */
    protected function getAllowedActions()
    {
        return $this->openActions;
    }

    /**
     * Dispatch request
     *
     * @param RequestInterface $request
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function dispatch(RequestInterface $request)
    {
        if (!$this->getRequest()->isDispatched()) {
            parent::dispatch($request);
        }

        $action = strtolower($this->getRequest()->getActionName());
        $pattern = '/^(' . implode('|', $this->getAllowedActions()) . ')$/i';

        if (!preg_match($pattern, $action)) {
            if (!$this->_getSession()->authenticate($this)) {
                $this->_actionFlag->set('', 'no-dispatch', true);
            }
        } else {
            $this->_getSession()->setNoReferer(true);
        }
        $this->_view->getPage()->getConfig()->addBodyClass('account');
        $result = parent::dispatch($request);
        $this->_getSession()->unsNoReferer(false);
        return $result;
    }
}
