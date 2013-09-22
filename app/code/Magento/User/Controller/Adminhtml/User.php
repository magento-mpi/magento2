<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_User
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\User\Controller\Adminhtml;

class User extends \Magento\Backend\Controller\ActionAbstract
{

    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\Controller\Context $context
     * @param \Magento\Core\Model\Registry $coreRegistry
     */
    public function __construct(
        \Magento\Backend\Controller\Context $context,
        \Magento\Core\Model\Registry $coreRegistry
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('Magento_User::system_acl_users')
            ->_addBreadcrumb(__('System'), __('System'))
            ->_addBreadcrumb(__('Permissions'), __('Permissions'))
            ->_addBreadcrumb(__('Users'), __('Users'))
        ;
        return $this;
    }

    public function indexAction()
    {
        $this->_title(__('Users'));
        $this->_initAction();
        $this->renderLayout();
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        $this->_title(__('Users'));

        $userId = $this->getRequest()->getParam('user_id');
        $model = \Mage::getModel('Magento\User\Model\User');

        if ($userId) {
            $model->load($userId);
            if (! $model->getId()) {
                $this->_session->addError(__('This user no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
        } else {
            $model->setInterfaceLocale(\Magento\Core\Model\LocaleInterface::DEFAULT_LOCALE);
        }

        $this->_title($model->getId() ? $model->getName() : __('New User'));

        // Restore previously entered form data from session
        $data = $this->_session->getUserData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        $this->_coreRegistry->register('permissions_user', $model);

        if (isset($userId)) {
            $breadcrumb = __('Edit User');
        } else {
            $breadcrumb = __('New User');
        }
        $this->_initAction()->_addBreadcrumb($breadcrumb, $breadcrumb);
        $this->renderLayout();
    }

    public function saveAction()
    {
        $userId = (int)$this->getRequest()->getParam('user_id');
        $data = $this->getRequest()->getPost();
        if (!$data) {
            $this->_redirect('*/*/');
            return;
        }
        /** @var $model \Magento\User\Model\User */
        $model = $this->_objectManager->create('Magento\User\Model\User')->load($userId);
        if ($userId && $model->isObjectNew()) {
            $this->_getSession()->addError(__('This user no longer exists.'));
            $this->_redirect('*/*/');
            return;
        }
        $model->setData($this->_getAdminUserData($data));
        $uRoles = $this->getRequest()->getParam('roles', array());
        if (count($uRoles)) {
            $model->setRoleId($uRoles[0]);
        }

        $currentUser = $this->_objectManager->get('Magento\Backend\Model\Auth\Session')->getUser();
        if ($userId == $currentUser->getId()
            && $this->_objectManager->get('Magento\Core\Model\Locale\Validator')->isValid($data['interface_locale'])
        ) {
            $this->_objectManager->get('Magento\Backend\Model\Locale\Manager')
                ->switchBackendInterfaceLocale($data['interface_locale']);
        }

        try {
            $model->save();
            $this->_getSession()->addSuccess(__('You saved the user.'));
            $this->_getSession()->setUserData(false);
            $this->_redirect('*/*/');
        } catch (\Magento\Core\Exception $e) {
            $this->_getSession()->addMessages($e->getMessages());
            $this->_getSession()->setUserData($data);
            $this->_redirect('*/*/edit', array('_current' => true));
        }
    }

    /**
     * Retrieve well-formed admin user data from the form input
     *
     * @param array $data
     * @return array
     */
    protected function _getAdminUserData(array $data)
    {
        if (isset($data['password']) && $data['password'] === '') {
            unset($data['password']);
        }
        if (isset($data['password_confirmation']) && $data['password_confirmation'] === '') {
            unset($data['password_confirmation']);
        }
        return $data;
    }

    public function deleteAction()
    {
        $currentUser = $this->_objectManager->get('Magento\Backend\Model\Auth\Session')->getUser();

        if ($userId = $this->getRequest()->getParam('user_id')) {
            if ( $currentUser->getId() == $userId ) {
                $this->_session->addError(
                    __('You cannot delete your own account.')
                );
                $this->_redirect('*/*/edit', array('user_id' => $userId));
                return;
            }
            try {
                $model = \Mage::getModel('Magento\User\Model\User');
                $model->setId($userId);
                $model->delete();
                $this->_session->addSuccess(__('You deleted the user.'));
                $this->_redirect('*/*/');
                return;
            }
            catch (\Exception $e) {
                $this->_session->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('user_id' => $this->getRequest()->getParam('user_id')));
                return;
            }
        }
        $this->_session->addError(__('We can\'t find a user to delete.'));
        $this->_redirect('*/*/');
    }

    public function rolesGridAction()
    {
        $userId = $this->getRequest()->getParam('user_id');
        $model = \Mage::getModel('Magento\User\Model\User');

        if ($userId) {
            $model->load($userId);
        }
        $this->_coreRegistry->register('permissions_user', $model);
        $this->loadLayout();
        $this->renderLayout();
    }

    public function roleGridAction()
    {
        $this->loadLayout(false);
        $this->renderLayout();
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_User::acl_users');
    }

}
