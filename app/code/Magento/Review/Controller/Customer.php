<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Review
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Review\Controller;

use Magento\App\Action\NotFoundException;
use Magento\App\RequestInterface;

/**
 * Customer reviews controller
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Customer extends \Magento\App\Action\Action
{
    /**
     * Customer session model
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @param \Magento\App\Action\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     */
    public function __construct(
        \Magento\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->_customerSession = $customerSession;
        parent::__construct($context);
    }

    /**
     * Check customer authentication for some actions
     *
     * @param RequestInterface $request
     * @return \Magento\App\ResponseInterface
     */
    public function dispatch(RequestInterface $request)
    {
        if (!$this->_customerSession->authenticate($this)) {
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
        }
        return parent::dispatch($request);
    }

    /**
     * Render my product reviews
     *
     * @return void
     */
    public function indexAction()
    {
        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();

        if ($navigationBlock = $this->_view->getLayout()->getBlock('customer_account_navigation')) {
            $navigationBlock->setActive('review/customer');
        }
        if ($block = $this->_view->getLayout()->getBlock('review_customer_list')) {
            $block->setRefererUrl($this->_redirect->getRefererUrl());
        }

        $this->_view->getLayout()->getBlock('head')->setTitle(__('My Product Reviews'));

        $this->_view->renderLayout();
    }

    /**
     * Render review details
     *
     * @return void
     */
    public function viewAction()
    {
        $this->_view->loadLayout();
        if ($navigationBlock = $this->_view->getLayout()->getBlock('customer_account_navigation')) {
            $navigationBlock->setActive('review/customer');
        }
        $this->_view->getLayout()->getBlock('head')->setTitle(__('Review Details'));
        $this->_view->renderLayout();
    }
}
