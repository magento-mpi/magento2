<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer groups controller
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Controller\Customer;

class Group extends \Magento\Backend\App\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Core\Model\Registry $coreRegistry
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Core\Model\Registry $coreRegistry
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    protected function _initGroup()
    {
        $this->_title(__('Customer Groups'));

        $this->_coreRegistry->register('current_group', $this->_objectManager->create('Magento\Customer\Model\Group'));
        $groupId = $this->getRequest()->getParam('id');
        if (!is_null($groupId)) {
            $this->_coreRegistry->registry('current_group')->load($groupId);
        }

    }

    /**
     * Customer groups list.
     */
    public function indexAction()
    {
        $this->_title(__('Customer Groups'));

        $this->loadLayout();
        $this->_setActiveMenu('Magento_Customer::customer_group');
        $this->_addBreadcrumb(__('Customers'), __('Customers'));
        $this->_addBreadcrumb(__('Customer Groups'), __('Customer Groups'));
        $this->renderLayout();
    }

    /**
     * Edit or create customer group.
     */
    public function newAction()
    {
        $this->_initGroup();
        $this->loadLayout();
        $this->_setActiveMenu('Magento_Customer::customer_group');
        $this->_addBreadcrumb(__('Customers'), __('Customers'));
        $this->_addBreadcrumb(__('Customer Groups'), __('Customer Groups'), $this->getUrl('adminhtml/customer_group'));

        $currentGroup = $this->_coreRegistry->registry('current_group');

        if (!is_null($currentGroup->getId())) {
            $this->_addBreadcrumb(__('Edit Group'), __('Edit Customer Groups'));
        } else {
            $this->_addBreadcrumb(__('New Group'), __('New Customer Groups'));
        }

        $this->_title($currentGroup->getId() ? $currentGroup->getCode() : __('New Customer Group'));

        $this->getLayout()->addBlock('Magento\Adminhtml\Block\Customer\Group\Edit', 'group', 'content')
            ->setEditMode((bool)$this->_coreRegistry->registry('current_group')->getId());

        $this->renderLayout();
    }

    /**
     * Edit customer group action. Forward to new action.
     */
    public function editAction()
    {
        $this->_forward('new');
    }

    /**
     * Create or save customer group.
     */
    public function saveAction()
    {
        $customerGroup = $this->_objectManager->create('Magento\Customer\Model\Group');
        $id = $this->getRequest()->getParam('id');
        if (!is_null($id)) {
            $customerGroup->load((int)$id);
        }

        $taxClass = (int)$this->getRequest()->getParam('tax_class');

        if ($taxClass) {
            try {
                $customerGroupCode = (string)$this->getRequest()->getParam('code');

                if (!empty($customerGroupCode)) {
                    $customerGroup->setCode($customerGroupCode);
                }

                $customerGroup->setTaxClassId($taxClass)->save();
                $this->_objectManager->get('Magento\Adminhtml\Model\Session')->addSuccess(__('The customer group has been saved.'));
                $this->getResponse()->setRedirect($this->getUrl('adminhtml/customer_group'));
                return;
            } catch (\Exception $e) {
                $this->_objectManager->get('Magento\Adminhtml\Model\Session')->addError($e->getMessage());
                $this->_objectManager->get('Magento\Adminhtml\Model\Session')->setCustomerGroupData($customerGroup->getData());
                $this->getResponse()->setRedirect($this->getUrl('adminhtml/customer_group/edit', array('id' => $id)));
                return;
            }
        } else {
            $this->_forward('new');
        }
    }

    /**
     * Delete customer group action
     */
    public function deleteAction()
    {
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            $customerGroup = $this->_objectManager->create('Magento\Customer\Model\Group')->load($id);
            if (!$customerGroup->getId()) {
                $this->_objectManager->get('Magento\Adminhtml\Model\Session')->addError(__('The customer group no longer exists.'));
                $this->_redirect('adminhtml/*/');
                return;
            }
            try {
                $customerGroup->delete();
                $this->_objectManager->get('Magento\Adminhtml\Model\Session')->addSuccess(__('The customer group has been deleted.'));
                $this->getResponse()->setRedirect($this->getUrl('adminhtml/customer_group'));
                return;
            } catch (\Exception $e) {
                $this->_objectManager->get('Magento\Adminhtml\Model\Session')->addError($e->getMessage());
                $this->getResponse()->setRedirect($this->getUrl('adminhtml/customer_group/edit', array('id' => $id)));
                return;
            }
        }

        $this->_redirect('adminhtml/customer_group');
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Customer::group');
    }
}
