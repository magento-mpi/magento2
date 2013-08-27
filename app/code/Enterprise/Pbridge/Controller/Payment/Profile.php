<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Pbridge
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Saved Payment (CC profiles) controller
 *
 * @category    Enterprise
 * @package     Enterprise_Pbridge
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Pbridge_Controller_Payment_Profile extends Magento_Core_Controller_Front_Action
{
    /**
     * Check whether Payment Profiles functionality enabled
     *
     * @return Enterprise_Pbridge_Controller_Payment_Profile
     */
    public function preDispatch()
    {
        parent::preDispatch();
        if (!$this->_objectManager->get('Enterprise_Pbridge_Helper_Data')->arePaymentProfilesEnables()) {
            if ($this->getRequest()->getActionName() != 'noroute') {
                $this->_forward('noroute');
            }
        }
        return $this;
    }

    /**
     * Payment Bridge frame with Saved Payment profiles
     */
    public function indexAction()
    {
        if(!Mage::getSingleton('Magento_Customer_Model_Session')->getCustomerId()) {
            Mage::getSingleton('Magento_Customer_Model_Session')->authenticate($this);
            return;
        }

        $this->loadLayout();
        $this->renderLayout();
    }
}
