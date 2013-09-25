<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Pbridge
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Saved Payment (CC profiles) controller
 *
 * @category    Magento
 * @package     Magento_Pbridge
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Pbridge_Controller_Payment_Profile extends Magento_Core_Controller_Front_Action
{
    /**
     * Customer session
     *
     * @var Magento_Customer_Model_Session
     */
    protected $_customerSession;

    /**
     * Construct
     *
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
     * Check whether Payment Profiles functionality enabled
     *
     * @return Magento_Pbridge_Controller_Payment_Profile
     */
    public function preDispatch()
    {
        parent::preDispatch();
        if (!$this->_objectManager->get('Magento_Pbridge_Helper_Data')->arePaymentProfilesEnables()) {
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
        if(!$this->_customerSession->getCustomerId()) {
            $this->_customerSession->authenticate($this);
            return;
        }

        $this->loadLayout();
        $this->renderLayout();
    }
}
