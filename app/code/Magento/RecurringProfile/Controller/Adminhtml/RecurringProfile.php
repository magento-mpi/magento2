<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Recurring profiles view/management controller
 *
 * TODO: implement ACL restrictions
 */
namespace Magento\RecurringProfile\Controller\Adminhtml;

use Magento\App\Action\NotFoundException;
use Magento\Core\Exception as CoreException;
use Magento\Customer\Controller\RegistryConstants;

class RecurringProfile extends \Magento\Backend\App\Action
{
    /**#@+
     * Request parameter keys
     */
    const PARAM_CUSTOMER_ID = 'id';
    const PARAM_PROFILE = 'profile';
    const PARAM_ACTION = 'action';
    /**#@-*/

    /**#@+
     * Values for PARAM_ACTION request parameter
     */
    const ACTION_CANCEL = 'cancel';
    const ACTION_SUSPEND = 'suspend';
    const ACTION_ACTIVATE = 'activate';
    /**#@-*/

    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Customer\Service\V1\CustomerServiceInterface
     */
    protected $_customerService;

    /**
     * @var \Magento\Logger
     */
    protected $_logger;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Core\Model\Registry $coreRegistry
     * @param \Magento\Customer\Service\V1\CustomerServiceInterface $customerService
     * @param \Magento\Logger $logger
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Core\Model\Registry $coreRegistry,
        \Magento\Customer\Service\V1\CustomerServiceInterface $customerService,
        \Magento\Logger $logger
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_customerService = $customerService;
        $this->_logger = $logger;
        parent::__construct($context);
    }

    /**
     * Recurring profiles list
     */
    public function indexAction()
    {
        $this->_title->add(__('Recurring Billing Profiles'));
        $this->_view->loadLayout();
        $this->_setActiveMenu('Magento_RecurringProfile::recurring_profile');
        $this->_view->renderLayout();
    }

    /**
     * View recurring profile details
     */
    public function viewAction()
    {
        try {
            $this->_title->add(__('Recurring Billing Profiles'));
            $profile = $this->_initProfile();
            $this->_view->loadLayout();
            $this->_setActiveMenu('Magento_RecurringProfile::recurring_profile');
            $this->_title->add(__('Profile #%1', $profile->getReferenceId()));
            $this->_view->renderLayout();
            return;
        } catch (CoreException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->_logger->logException($e);
        }
        $this->_redirect('sales/*/');
    }

    /**
     * Profiles ajax grid
     */
    public function gridAction()
    {
        try {
            $this->_view->loadLayout()->renderLayout();
            return;
        } catch (CoreException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->_logger->logException($e);
        }
        $this->_redirect('sales/*/');
    }

    /**
     * Profile orders ajax grid
     *
     * @throws NotFoundException
     */
    public function ordersAction()
    {
        try {
            $this->_initProfile();
            $this->_view->loadLayout()->renderLayout();
        } catch (\Exception $e) {
            $this->_logger->logException($e);
            throw new NotFoundException();
        }
    }

    /**
     * Profile state updater action
     */
    public function updateStateAction()
    {
        $profile = null;
        try {
            $profile = $this->_initProfile();
            $action = $this->getRequest()->getParam(self::PARAM_ACTION);

            switch ($action) {
                case self::ACTION_CANCEL:
                    $profile->cancel();
                    break;
                case self::ACTION_SUSPEND:
                    $profile->suspend();
                    break;
                case self::ACTION_ACTIVATE:
                    $profile->activate();
                    break;
                default:
                    throw new \Exception(sprintf('Wrong action parameter: %s', $action));
            }
            $this->messageManager->addSuccess(__('The profile state has been updated.'));
        } catch (CoreException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addError(__('We could not update the profile.'));
            $this->_logger->logException($e);
        }
        if ($profile) {
            $this->_redirect('sales/*/view', array(self::PARAM_PROFILE => $profile->getId()));
        } else {
            $this->_redirect('sales/*/');
        }
    }

    /**
     * Profile information updater action
     */
    public function updateProfileAction()
    {
        $profile = null;
        try {
            $profile = $this->_initProfile();
            $profile->fetchUpdate();
            if ($profile->hasDataChanges()) {
                $profile->save();
                $this->messageManager->addSuccess(__('You updated the profile.'));
            } else {
                $this->messageManager->addNotice(__('The profile has no changes.'));
            }
        } catch (CoreException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addError(__('We could not update the profile.'));
            $this->_logger->logException($e);
        }
        if ($profile) {
            $this->_redirect('sales/*/view', array(self::PARAM_PROFILE => $profile->getId()));
        } else {
            $this->_redirect('sales/*/');
        }
    }

    /**
     * Customer billing agreements ajax action
     *
     */
    public function customerGridAction()
    {
        $customerId = (int)$this->getRequest()->getParam(self::PARAM_CUSTOMER_ID);

        if ($customerId) {
            $this->_coreRegistry->register(RegistryConstants::CURRENT_CUSTOMER_ID, $customerId);
        }

        $this->_view->loadLayout(false);
        $this->_view->renderLayout();
    }

    /**
     * Load/set profile
     *
     * @return \Magento\RecurringProfile\Model\Profile
     * @throws \Magento\Core\Exception
     */
    protected function _initProfile()
    {
        $profile = $this->_objectManager->create('Magento\RecurringProfile\Model\Profile')
            ->load($this->getRequest()->getParam(self::PARAM_PROFILE));
        if (!$profile->getId()) {
            throw new CoreException(__('The profile you specified does not exist.'));
        }
        $this->_coreRegistry->register('current_recurring_profile', $profile);
        return $profile;
    }
}
