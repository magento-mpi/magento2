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
namespace Magento\Pbridge\Controller\Payment;

class Profile extends \Magento\Core\Controller\Front\Action
{
    /**
     * Check whether Payment Profiles functionality enabled
     *
     * @return \Magento\Pbridge\Controller\Payment\Profile
     */
    public function preDispatch()
    {
        parent::preDispatch();
        if (!\Mage::helper('Magento\Pbridge\Helper\Data')->arePaymentProfilesEnables()) {
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
        if(!\Mage::getSingleton('Magento\Customer\Model\Session')->getCustomerId()) {
            \Mage::getSingleton('Magento\Customer\Model\Session')->authenticate($this);
            return;
        }

        $this->loadLayout();
        $this->renderLayout();
    }
}
