<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Reward
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Reward admin customer controller
 *
 * @category    Enterprise
 * @package     Enterprise_Reward
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Reward_Controller_Adminhtml_Customer_Reward extends Magento_Adminhtml_Controller_Action
{
    /**
     * Check if module functionality enabled
     *
     * @return Enterprise_Reward_Controller_Adminhtml_Reward_Rate
     */
    public function preDispatch()
    {
        parent::preDispatch();
        if (!Mage::helper('Enterprise_Reward_Helper_Data')->isEnabled()
            && $this->getRequest()->getActionName() != 'noroute'
        ) {
            $this->_forward('noroute');
        }
        return $this;
    }

    /**
     * History Ajax Action
     */
    public function historyAction()
    {
        $customerId = $this->getRequest()->getParam('id', 0);
        $history = $this->getLayout()
            ->createBlock('Enterprise_Reward_Block_Adminhtml_Customer_Edit_Tab_Reward_History', '',
                array('data' => array('customer_id' => $customerId)));
        $this->getResponse()->setBody($history->toHtml());
    }

    /**
     * History Grid Ajax Action
     *
     */
    public function historyGridAction()
    {
        $customerId = $this->getRequest()->getParam('id', 0);
        $history = $this->getLayout()
            ->createBlock('Enterprise_Reward_Block_Adminhtml_Customer_Edit_Tab_Reward_History_Grid', '',
                array('data' => array('customer_id' => $customerId)));
        $this->getResponse()->setBody($history->toHtml());
    }

    /**
     *  Delete orphan points Action
     */
    public function deleteOrphanPointsAction()
    {
        $customerId = $this->getRequest()->getParam('id', 0);
        if ($customerId) {
            try {
                Mage::getModel('Enterprise_Reward_Model_Reward')
                    ->deleteOrphanPointsByCustomer($customerId);
                $this->_getSession()
                    ->addSuccess(Mage::helper('Enterprise_Reward_Helper_Data')->__('You removed the orphan points.'));
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/customer/edit', array('_current' => true));
    }

    /**
     * Acl check for admin
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(Enterprise_Reward_Helper_Data::XML_PATH_PERMISSION_BALANCE);
    }
}
