<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Recurring profiles view/management controller
 */
namespace Magento\Sales\Controller\Recurring;

class Profile extends \Magento\App\Action\Action
{
    /**
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $_session = null;

    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\App\Action\Context $context
     * @param \Magento\Core\Model\Registry $coreRegistry
     */
    public function __construct(
        \Magento\App\Action\Context $context,
        \Magento\Core\Model\Registry $coreRegistry
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * Make sure customer is logged in and put it into registry
     */
    public function preDispatch()
    {
        parent::preDispatch();
        if (!$this->getRequest()->isDispatched()) {
            return;
        }
        $this->_session = $this->_objectManager->get('Magento\Customer\Model\Session');
        if (!$this->_session->authenticate($this)) {
            $this->setFlag('', 'no-dispatch', true);
        }
        $this->_coreRegistry->register('current_customer', $this->_session->getCustomer());
    }

    /**
     * Profiles listing
     */
    public function indexAction()
    {
        $this->_title(__('Recurring Billing Profiles'));
        $this->loadLayout();
        $this->_initLayoutMessages('Magento\Customer\Model\Session');
        $this->renderLayout();
    }

    /**
     * Profile main view
     */
    public function viewAction()
    {
        $this->_viewAction();
    }

    /**
     * Attempt to set profile state
     */
    public function updateStateAction()
    {
        $profile = null;
        try {
            $profile = $this->_initProfile();

            switch ($this->getRequest()->getParam('action')) {
                case 'cancel':
                    $profile->cancel();
                    break;
                case 'suspend':
                    $profile->suspend();
                    break;
                case 'activate':
                    $profile->activate();
                    break;
                default:
                    break;
            }
            $this->_session->addSuccess(__('The profile state has been updated.'));
        } catch (\Magento\Core\Exception $e) {
            $this->_session->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->_session->addError(__('We couldn\'t update the profile.'));
            $this->_objectManager->get('Magento\Logger')->logException($e);
        }
        if ($profile) {
            $this->_redirect('*/*/view', array('profile' => $profile->getId()));
        } else {
            $this->_redirect('*/*/');
        }
    }

    /**
     * Fetch an update with profile
     */
    public function updateProfileAction()
    {
        $profile = null;
        try {
            $profile = $this->_initProfile();
            $profile->fetchUpdate();
            if ($profile->hasDataChanges()) {
                $profile->save();
                $this->_session->addSuccess(__('The profile has been updated.'));
            } else {
                $this->_session->addNotice(__('The profile has no changes.'));
            }
        } catch (\Magento\Core\Exception $e) {
            $this->_session->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->_session->addError(__('We couldn\'t update the profile.'));
            $this->_objectManager->get('Magento\Logger')->logException($e);
        }
        if ($profile) {
            $this->_redirect('*/*/view', array('profile' => $profile->getId()));
        } else {
            $this->_redirect('*/*/');
        }
    }

    /**
     * Generic profile view action
     */
    protected function _viewAction()
    {
        try {
            $profile = $this->_initProfile();
            $this->_title(__('Recurring Billing Profiles'))->_title(__('Profile #%1', $profile->getReferenceId()));
            $this->loadLayout();
            $this->_initLayoutMessages('Magento\Customer\Model\Session');
            $navigationBlock = $this->getLayout()->getBlock('customer_account_navigation');
            if ($navigationBlock) {
                $navigationBlock->setActive('sales/recurring_profile/');
            }
            $this->renderLayout();
            return;
        } catch (\Magento\Core\Exception $e) {
            $this->_session->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->_objectManager->get('Magento\Logger')->logException($e);
        }
        $this->_redirect('*/*/');
    }

    /**
     * Instantiate current profile and put it into registry
     *
     * @return \Magento\Sales\Model\Recurring\Profile
     * @throws \Magento\Core\Exception
     */
    protected function _initProfile()
    {
        $profile = $this->_objectManager->create('Magento\Sales\Model\Recurring\Profile')
            ->load($this->getRequest()->getParam('profile'));
        if (!$profile->getId()) {
            throw new \Magento\Core\Exception(__('We can\'t find the profile you specified.'));
        }
        $this->_coreRegistry->register('current_recurring_profile', $profile);
        return $profile;
    }
}
