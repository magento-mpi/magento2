<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Review
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer reviews controller
 *
 * @category    Magento
 * @package     Magento_Review
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Review\Controller;

class Customer extends \Magento\Core\Controller\Front\Action
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @param \Magento\Core\Controller\Varien\Action\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     */
    public function __construct(
        \Magento\Core\Controller\Varien\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->_customerSession = $customerSession;
        parent::__construct($context);
    }

    /**
     * Action predispatch
     *
     * Check customer authentication for some actions
     */
    public function preDispatch()
    {
        parent::preDispatch();
        if (!$this->_customerSession->authenticate($this)) {
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
        }
    }

    public function indexAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('Magento\Catalog\Model\Session');

        if ($navigationBlock = $this->getLayout()->getBlock('customer_account_navigation')) {
            $navigationBlock->setActive('review/customer');
        }
        if ($block = $this->getLayout()->getBlock('review_customer_list')) {
            $block->setRefererUrl($this->_getRefererUrl());
        }

        $this->getLayout()->getBlock('head')->setTitle(__('My Product Reviews'));

        $this->renderLayout();
    }

    public function viewAction()
    {
        $this->loadLayout();
        if ($navigationBlock = $this->getLayout()->getBlock('customer_account_navigation')) {
            $navigationBlock->setActive('review/customer');
        }
        $this->getLayout()->getBlock('head')->setTitle(__('Review Details'));
        $this->renderLayout();
    }
}
