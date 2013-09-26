<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Magento_User roles controller
 */
class Magento_User_Controller_Adminhtml_User_Role extends Magento_Backend_Controller_ActionAbstract
{

    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * Factory for user role model
     *
     * @var Magento_User_Model_RoleFactory
     */
    protected $_roleFactory;

    /**
     * User model factory
     *
     * @var Magento_User_Model_UserFactory
     */
    protected $_userFactory;

    /**
     * Rules model factory
     *
     * @var Magento_User_Model_RulesFactory
     */
    protected $_rulesFactory;

    /**
     * Backend auth session
     *
     * @var Magento_Backend_Model_Auth_Session
     */
    protected $_authSession;

    /**
     * Construct
     *
     * @param Magento_Backend_Controller_Context $context
     * @param Magento_Core_Model_Registry $coreRegistry
     * @param Magento_User_Model_RoleFactory $roleFactory
     * @param Magento_User_Model_UserFactory $userFactory
     * @param Magento_User_Model_RulesFactory $rulesFactory
     * @param Magento_Backend_Model_Auth_Session $authSession
     */
    public function __construct(
        Magento_Backend_Controller_Context $context,
        Magento_Core_Model_Registry $coreRegistry,
        Magento_User_Model_RoleFactory $roleFactory,
        Magento_User_Model_UserFactory $userFactory,
        Magento_User_Model_RulesFactory $rulesFactory,
        Magento_Backend_Model_Auth_Session $authSession
    ) {
        parent::__construct($context);
        $this->_coreRegistry = $coreRegistry;
        $this->_roleFactory = $roleFactory;
        $this->_userFactory = $userFactory;
        $this->_rulesFactory = $rulesFactory;
        $this->_authSession = $authSession;
    }

    /**
     * Preparing layout for output
     *
     * @return Magento_User_Controller_Adminhtml_User_Role
     */
    protected function _initAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('Magento_User::system_acl_roles');
        $this->_addBreadcrumb(__('System'), __('System'));
        $this->_addBreadcrumb(__('Permissions'), __('Permissions'));
        $this->_addBreadcrumb(__('Roles'), __('Roles'));
        return $this;
    }

    /**
     * Initialize role model by passed parameter in request
     *
     * @param string $requestVariable
     * @return Magento_User_Model_Role
     */
    protected function _initRole($requestVariable = 'rid')
    {
        $this->_title(__('Roles'));

        $role = $this->_roleFactory->create()->load($this->getRequest()->getParam($requestVariable));
        // preventing edit of relation role
        if ($role->getId() && $role->getRoleType() != 'G') {
            $role->unsetData($role->getIdFieldName());
        }

        $this->_coreRegistry->register('current_role', $role);
        return $this->_coreRegistry->registry('current_role');
    }

    /**
     * Show grid with roles existing in systems
     *
     */
    public function indexAction()
    {
        $this->_title(__('Roles'));

        $this->_initAction();

        $this->renderLayout();
    }

    /**
     * Action for ajax request from grid
     *
     */
    public function roleGridAction()
    {
        $this->loadLayout(false);
        $this->renderLayout();
    }

    /**
     * Edit role action
     *
     */
    public function editRoleAction()
    {
        $role = $this->_initRole();
        $this->_initAction();

        if ($role->getId()) {
            $breadCrumb      = __('Edit Role');
            $breadCrumbTitle = __('Edit Role');
        } else {
            $breadCrumb = __('Add New Role');
            $breadCrumbTitle = __('Add New Role');
        }

        $this->_title($role->getId() ? $role->getRoleName() : __('New Role'));

        $this->_addBreadcrumb($breadCrumb, $breadCrumbTitle);

        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
        $this->getLayout()->getBlock('adminhtml.user.role.buttons')
            ->setRoleId($role->getId())
            ->setRoleInfo($role);

        $this->renderLayout();
    }

    /**
     * Remove role action
     *
     */
    public function deleteAction()
    {
        $rid = $this->getRequest()->getParam('rid', false);
        /** @var Magento_User_Model_User $currentUser */
        $currentUser = $this->_userFactory->create()->setId($this->_authSession->getUser()->getId());

        if (in_array($rid, $currentUser->getRoles()) ) {
            $this->_session->addError(
                __('You cannot delete self-assigned roles.')
            );
            $this->_redirect('*/*/editrole', array('rid' => $rid));
            return;
        }

        try {
            $this->_initRole()->delete();

            $this->_session->addSuccess(
                __('You deleted the role.')
            );
        } catch (Exception $e) {
            $this->_session->addError(
                __('An error occurred while deleting this role.')
            );
        }

        $this->_redirect("*/*/");
    }

    /**
     * Role form submit action to save or create new role
     *
     */
    public function saveRoleAction()
    {
        $rid        = $this->getRequest()->getParam('role_id', false);
        $resource   = $this->getRequest()->getParam('resource', false);
        $roleUsers  = $this->getRequest()->getParam('in_role_user', null);
        parse_str($roleUsers, $roleUsers);
        $roleUsers = array_keys($roleUsers);

        $oldRoleUsers = $this->getRequest()->getParam('in_role_user_old');
        parse_str($oldRoleUsers, $oldRoleUsers);
        $oldRoleUsers = array_keys($oldRoleUsers);

        $isAll = $this->getRequest()->getParam('all');
        if ($isAll) {
            $resource = array($this->_objectManager->get('Magento_Core_Model_Acl_RootResource')->getId());
        }

        $role = $this->_initRole('role_id');
        if (!$role->getId() && $rid) {
            $this->_session->addError(__('This role no longer exists.'));
            $this->_redirect('*/*/');
            return;
        }

        try {
            $roleName = $this->getRequest()->getParam('rolename', false);

            $role->setName($roleName)
                 ->setPid($this->getRequest()->getParam('parent_id', false))
                 ->setRoleType('G');
            $this->_eventManager->dispatch(
                'admin_permissions_role_prepare_save',
                array('object' => $role, 'request' => $this->getRequest())
            );
            $role->save();

            $this->_rulesFactory->create()
                ->setRoleId($role->getId())
                ->setResources($resource)
                ->saveRel();

            foreach ($oldRoleUsers as $oUid) {
                $this->_deleteUserFromRole($oUid, $role->getId());
            }

            foreach ($roleUsers as $nRuid) {
                $this->_addUserToRole($nRuid, $role->getId());
            }

            $this->_session->addSuccess(
                __('You saved the role.')
            );
        } catch (Magento_Core_Exception $e) {
            $this->_session->addError($e->getMessage());
        } catch (Exception $e) {
            $this->_session->addError(
                __('An error occurred while saving this role.')
            );
        }
        $this->_redirect('*/*/');
        return;
    }

    /**
     * Action for ajax request from assigned users grid
     */
    public function editrolegridAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Remove user from role
     *
     * @param int $userId
     * @param int $roleId
     * @return bool
     */
    protected function _deleteUserFromRole($userId, $roleId)
    {
        try {
            $this->_userFactory->create()
                ->setRoleId($roleId)
                ->setUserId($userId)
                ->deleteFromRole();
        } catch (Exception $e) {
            throw $e;
            return false;
        }
        return true;
    }

    /**
     * Assign user to role
     *
     * @param int $userId
     * @param int $roleId
     * @return bool
     */
    protected function _addUserToRole($userId, $roleId)
    {
        $user = $this->_userFactory->create()->load($userId);
        $user->setRoleId($roleId);

        if ($user->roleUserExists() === true ) {
            return false;
        } else {
            $user->save();
            return true;
        }
    }

    /**
     * Acl checking
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_User::acl_roles');
    }
}
