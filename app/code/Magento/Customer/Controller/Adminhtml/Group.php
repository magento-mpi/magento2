<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Controller\Adminhtml;

use Magento\Customer\Controller\RegistryConstants;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Customer\Service\V1\CustomerGroupServiceInterface;
use Magento\Customer\Service\V1\Data\CustomerGroup;
use Magento\Customer\Service\V1\Data\CustomerGroupBuilder;
use Magento\Exception\InputException;

/**
 * Customer groups controller
 */
class Group extends \Magento\Backend\App\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var CustomerGroupServiceInterface
     */
    protected $_groupService;

    /**
     * @var CustomerGroupBuilder
     */
    protected $_customerGroupBuilder;

    /**
     * Initialize Group Controller
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param CustomerGroupServiceInterface $groupService
     * @param CustomerGroupBuilder $customerGroupBuilder
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        CustomerGroupServiceInterface $groupService,
        CustomerGroupBuilder $customerGroupBuilder
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_groupService = $groupService;
        $this->_customerGroupBuilder = $customerGroupBuilder;
        parent::__construct($context);
    }

    /**
     * Initialize current group and set it in the registry.
     *
     * @return int
     */
    protected function _initGroup()
    {
        $this->_title->add(__('Customer Groups'));

        $groupId = $this->getRequest()->getParam('id');
        $this->_coreRegistry->register(RegistryConstants::CURRENT_GROUP_ID, $groupId);

        return $groupId;
    }

    /**
     * Customer groups list.
     *
     * @return void
     */
    public function indexAction()
    {
        $this->_title->add(__('Customer Groups'));

        $this->_view->loadLayout();
        $this->_setActiveMenu('Magento_Customer::customer_group');
        $this->_addBreadcrumb(__('Customers'), __('Customers'));
        $this->_addBreadcrumb(__('Customer Groups'), __('Customer Groups'));
        $this->_view->renderLayout();
    }

    /**
     * Edit or create customer group.
     *
     * @return void
     */
    public function newAction()
    {
        $groupId = $this->_initGroup();

        $this->_view->loadLayout();
        $this->_setActiveMenu('Magento_Customer::customer_group');
        $this->_addBreadcrumb(__('Customers'), __('Customers'));
        $this->_addBreadcrumb(__('Customer Groups'), __('Customer Groups'), $this->getUrl('customer/group'));

        if (is_null($groupId)) {
            $this->_addBreadcrumb(__('New Group'), __('New Customer Groups'));
            $this->_title->add(__('New Customer Group'));
        } else {
            $this->_addBreadcrumb(__('Edit Group'), __('Edit Customer Groups'));
            $this->_title->add($this->_groupService->getGroup($groupId)->getCode());
        }

        $this->_view->getLayout()->addBlock(
            'Magento\Customer\Block\Adminhtml\Group\Edit',
            'group',
            'content'
        )->setEditMode(
            (bool)$groupId
        );

        $this->_view->renderLayout();
    }

    /**
     * Edit customer group action. Forward to new action.
     *
     * @return void
     */
    public function editAction()
    {
        $this->_forward('new');
    }

    /**
     * Create or save customer group.
     *
     * @return void
     */
    public function saveAction()
    {
        $taxClass = (int)$this->getRequest()->getParam('tax_class');

        /** @var CustomerGroup $customerGroup */
        $customerGroup = null;
        if ($taxClass) {
            $id = $this->getRequest()->getParam('id');
            try {
                if (!is_null($id)) {
                    $this->_customerGroupBuilder->populate($this->_groupService->getGroup((int)$id));
                }
                $customerGroupCode = (string)$this->getRequest()->getParam('code');
                if (empty($customerGroupCode)) {
                    $customerGroupCode = null;
                }
                $this->_customerGroupBuilder->setCode($customerGroupCode);
                $this->_customerGroupBuilder->setTaxClassId($taxClass);
                $customerGroup = $this->_customerGroupBuilder->create();

                $id = $this->_groupService->saveGroup($customerGroup);
                $this->messageManager->addSuccess(__('The customer group has been saved.'));
                $this->getResponse()->setRedirect($this->getUrl('customer/group'));
                return;
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                if ($customerGroup != null) {
                    $this->storeCustomerGroupDataToSession($customerGroup->__toArray());
                }
                $this->getResponse()->setRedirect($this->getUrl('customer/group/edit', array('id' => $id)));
                return;
            }
        } else {
            $this->_forward('new');
        }
    }

    /**
     * Delete customer group.
     *
     * @return void
     */
    public function deleteAction()
    {
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            try {
                $this->_groupService->deleteGroup($id);
                $this->messageManager->addSuccess(__('The customer group has been deleted.'));
                $this->getResponse()->setRedirect($this->getUrl('customer/group'));
                return;
            } catch (NoSuchEntityException $e) {
                $this->messageManager->addError(__('The customer group no longer exists.'));
                $this->getResponse()->setRedirect($this->getUrl('customer/*/'));
                return;
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->getResponse()->setRedirect($this->getUrl('customer/group/edit', array('id' => $id)));
                return;
            }
        }
        $this->_redirect('customer/group');
    }

    /**
     * Determine if authorized to perform group actions.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Customer::group');
    }

    /**
     * Store Customer Group Data to session
     *
     * @param array $customerGroupData
     * @return void
     */
    protected function storeCustomerGroupDataToSession($customerGroupData)
    {
        if (array_key_exists('code', $customerGroupData)) {
            $customerGroupData['customer_group_code'] = $customerGroupData['code'];
            unset($customerGroupData['code']);
        }
        $this->_getSession()->setCustomerGroupData($customerGroupData);
    }
}
