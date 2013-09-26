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

class Magento_Review_Controller_Customer extends Magento_Core_Controller_Front_Action
{
    /**
     * @var Magento_Customer_Model_Session
     */
    protected $_customerSession;

    /**
     * @param Magento_Core_Controller_Varien_Action_Context $context
     * @param Magento_Customer_Model_Session $customerSession
     */
    public function __construct(
        Magento_Core_Controller_Varien_Action_Context $context,
        Magento_Customer_Model_Session $customerSession
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
        $this->_initLayoutMessages('Magento_Catalog_Model_Session');

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
