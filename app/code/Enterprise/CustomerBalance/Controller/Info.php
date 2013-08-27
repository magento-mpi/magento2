<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_CustomerBalance
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customerbalance controller for My Account
 *
 */
class Enterprise_CustomerBalance_Controller_Info extends Magento_Core_Controller_Front_Action
{
    /**
     * Only logged in users can use this functionality,
     * this function checks if user is logged in before all other actions
     *
     */
    public function preDispatch()
    {
        parent::preDispatch();

        if (!Mage::getSingleton('Magento_Customer_Model_Session')->authenticate($this)) {
            $this->setFlag('', 'no-dispatch', true);
        }
    }

    /**
     * Store Credit dashboard
     *
     */
    public function indexAction()
    {
        if (!$this->_objectManager->get('Enterprise_CustomerBalance_Helper_Data')->isEnabled()) {
            $this->_redirect('customer/account/');
            return;
        }
        $this->loadLayout();
        $this->_initLayoutMessages('Magento_Customer_Model_Session');
        $this->loadLayoutUpdates();
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->setTitle(__('Store Credit'));
        }
        $this->renderLayout();
    }
}
