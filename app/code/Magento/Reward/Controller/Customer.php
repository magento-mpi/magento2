<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reward
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Reward customer controller
 *
 * @category    Magento
 * @package     Magento_Reward
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Reward\Controller;

use Magento\App\Action\NotFoundException;
use Magento\App\RequestInterface;

class Customer extends \Magento\App\Action\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Core\App\Action\FormKeyValidator
     */
    protected $_formKeyValidator;

    /**
     * @param \Magento\App\Action\Context $context
     * @param \Magento\Registry $coreRegistry
     * @param \Magento\Core\App\Action\FormKeyValidator $formKeyValidator
     */
    public function __construct(
        \Magento\App\Action\Context $context,
        \Magento\Registry $coreRegistry,
        \Magento\Core\App\Action\FormKeyValidator $formKeyValidator
    ) {
        $this->_formKeyValidator = $formKeyValidator;
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * Check is customer authenticate
     * Check is RP enabled on frontend
     *
     * @param RequestInterface $request
     * @return \Magento\App\ResponseInterface
     * @throws \Magento\App\Action\NotFoundException
     */
    public function dispatch(RequestInterface $request)
    {
        if (!$this->_objectManager->get('Magento\Customer\Model\Session')->authenticate($this)) {
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
        }
        if (!$this->_objectManager->get('Magento\Reward\Helper\Data')->isEnabledOnFront()) {
            throw new NotFoundException();
        }
        return parent::dispatch($request);
    }

    /**
     * Info Action
     *
     * @return void
     */
    public function infoAction()
    {
        $this->_coreRegistry->register('current_reward', $this->_getReward());
        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
        $headBlock = $this->_view->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->setTitle(__('Reward Points'));
        }
        $this->_view->renderLayout();
    }

    /**
     * Save settings
     *
     * @return void
     */
    public function saveSettingsAction()
    {
        if (!$this->_formKeyValidator->validate($this->getRequest())) {
            return $this->_redirect('*/*/info');
        }

        $customer = $this->_getCustomer();
        if ($customer->getId()) {
            $customer->setRewardUpdateNotification($this->getRequest()->getParam('subscribe_updates'))
                ->setRewardWarningNotification($this->getRequest()->getParam('subscribe_warnings'));
            $customer->getResource()->saveAttribute($customer, 'reward_update_notification');
            $customer->getResource()->saveAttribute($customer, 'reward_warning_notification');

            $this->messageManager->addSuccess(
                __('You saved the settings.')
            );
        }
        $this->_redirect('*/*/info');
    }

    /**
     * Unsubscribe customer from update/warning balance notifications
     *
     * @return void
     */
    public function unsubscribeAction()
    {
        $notification = $this->getRequest()->getParam('notification');
        if (!in_array($notification, array('update','warning'))) {
            $this->_forward('noroute');
        }

        try {
            /* @var $customer \Magento\Customer\Model\Session */
            $customer = $this->_getCustomer();
            if ($customer->getId()) {
                if ($notification == 'update') {
                    $customer->setRewardUpdateNotification(false);
                    $customer->getResource()->saveAttribute($customer, 'reward_update_notification');
                } elseif ($notification == 'warning') {
                    $customer->setRewardWarningNotification(false);
                    $customer->getResource()->saveAttribute($customer, 'reward_warning_notification');
                }
                $this->messageManager->addSuccess(
                    __('You have been unsubscribed.')
                );
            }
        } catch (\Exception $e) {
            $this->messageManager->addError(__('Failed to unsubscribe'));
        }

        $this->_redirect('*/*/info');
    }

    /**
     * Retrieve customer session model object
     *
     * @return \Magento\Customer\Model\Session
     */
    protected function _getSession()
    {
        return $this->_objectManager->get('Magento\Customer\Model\Session');
    }

    /**
     * Retrieve customer session model object
     *
     * @return \Magento\Customer\Model\Session
     */
    protected function _getCustomer()
    {
        return $this->_getSession()->getCustomer();
    }

    /**
     * Load reward by customer
     *
     * @return \Magento\Reward\Model\Reward
     */
    protected function _getReward()
    {
        $reward = $this->_objectManager->create('Magento\Reward\Model\Reward')
            ->setCustomer($this->_getCustomer())
            ->setWebsiteId($this->_objectManager->get('Magento\Core\Model\StoreManagerInterface')
                ->getStore()->getWebsiteId())
            ->loadByCustomer();
        return $reward;
    }
}
