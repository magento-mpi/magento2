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
     * Customer session
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * Construct
     *
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
     * Check whether Payment Profiles functionality enabled
     *
     * @return \Magento\Pbridge\Controller\Payment\Profile
     */
    public function preDispatch()
    {
        parent::preDispatch();
        if (!$this->_objectManager->get('Magento\Pbridge\Helper\Data')->arePaymentProfilesEnables()) {
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
