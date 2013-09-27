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
 * Reward admin customer controller
 *
 * @category    Magento
 * @package     Magento_Reward
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Reward_Controller_Adminhtml_Customer_Reward extends Magento_Adminhtml_Controller_Action
{
    /**
     * Check if module functionality enabled
     *
     * @return Magento_Reward_Controller_Adminhtml_Reward_Rate
     */
    public function preDispatch()
    {
        parent::preDispatch();
        if (!$this->_objectManager->get('Magento_Reward_Helper_Data')->isEnabled()
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
        $this->loadLayout(false)
            ->renderLayout();
    }

    /**
     * History Grid Ajax Action
     *
     */
    public function historyGridAction()
    {
        $this->loadLayout(false)
            ->renderLayout();
    }

    /**
     *  Delete orphan points Action
     */
    public function deleteOrphanPointsAction()
    {
        $customerId = $this->getRequest()->getParam('id', 0);
        if ($customerId) {
            try {
                $this->_objectManager->create('Magento_Reward_Model_Reward')
                    ->deleteOrphanPointsByCustomer($customerId);
                $this->_getSession()
                    ->addSuccess(__('You removed the orphan points.'));
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
        return $this->_authorization->isAllowed(Magento_Reward_Helper_Data::XML_PATH_PERMISSION_BALANCE);
    }
}
