<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Newsletter
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Customers newsletter subscription controller
 *
 * @category   Magento
 * @package    Magento_Newsletter
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Newsletter\Controller;

use Magento\App\Action\NotFoundException;
use Magento\App\RequestInterface;

class Manage extends \Magento\App\Action\Action
{
    /**
     * Customer session
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Core\App\Action\FormKeyValidator
     */
    protected $_formKeyValidator;

    /**
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param \Magento\App\Action\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Core\App\Action\FormKeyValidator $formKeyValidator
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Core\App\Action\FormKeyValidator $formKeyValidator,
        \Magento\Core\Model\StoreManagerInterface $storeManager
    ) {
        $this->_storeManager = $storeManager;
        parent::__construct($context);
        $this->_formKeyValidator = $formKeyValidator;
        $this->_customerSession = $customerSession;
    }

    /**
     * Check customer authentication for some actions
     *
     * @param RequestInterface $request
     * @return mixed
     */
    public function dispatch(RequestInterface $request)
    {
        if (!$this->_customerSession->authenticate($this)) {
            $this->_actionFlag->set('', 'no-dispatch', true);
        }
        return parent::dispatch($request);
    }

    public function indexAction()
    {
        $this->_view->loadLayout();
        $this->_view->getLayout()
            ->initMessages(array('Magento\Customer\Model\Session', 'Magento\Catalog\Model\Session'));

        if ($block = $this->_view->getLayout()->getBlock('customer_newsletter')) {
            $block->setRefererUrl($this->_redirect->getRefererUrl());
        }
        $this->_view->getLayout()->getBlock('head')->setTitle(__('Newsletter Subscription'));
        $this->_view->renderLayout();
    }

    public function saveAction()
    {
        if (!$this->_formKeyValidator->validate($this->getRequest())) {
            return $this->_redirect('customer/account/');
        }
        try {
            $this->_customerSession->getCustomer()
                ->setStoreId($this->_storeManager->getStore()->getId())
                ->setIsSubscribed((boolean)$this->getRequest()->getParam('is_subscribed', false))
                ->save();
            if ((boolean)$this->getRequest()->getParam('is_subscribed', false)) {
                $this->_customerSession->addSuccess(__('We saved the subscription.'));
            } else {
                $this->_customerSession->addSuccess(__('We removed the subscription.'));
            }
        }
        catch (\Exception $e) {
            $this->_customerSession->addError(__('Something went wrong while saving your subscription.'));
        }
        $this->_redirect('customer/account/');
    }
}
