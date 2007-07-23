<?php
/**
 * Adminhtml permissions users controller
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */
class Mage_Adminhtml_Permission_UserController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->loadLayout('baseframe');
        $this->_setActiveMenu('system/acl');
        $this->_addBreadcrumb(__('System'), __('System Title'));
        $this->_addBreadcrumb(__('Permissions'), __('Permissions Title'));
        $this->_addBreadcrumb(__('Users'), __('Users Title'));

        $this->_addContent($this->getLayout()->createBlock('adminhtml/permissions_users'));
        $this->renderLayout();
    }

    public function userGridAction()
    {
        $this->getResponse()->setBody($this->getLayout()->createBlock('adminhtml/permissions_grid_user')->toHtml());
    }

    public function editUserAction()
    {
        $userId = $this->getRequest()->getParam('id');
        $this->loadLayout('baseframe');

        if( intval($userId) > 0 ) {
            $breadCrumb = __('Edit User');
            $breadCrumbTitle = __('Edit User Title');
        } else {
            $breadCrumb = __('Add new User');
            $breadCrumbTitle = __('Add new User Title');
        }

        $this->_addBreadcrumb(__('System'), __('System Title'));
        $this->_addBreadcrumb(__('Permission'), __('Permission Title'));
        $this->_addBreadcrumb(__('Users'), __('Users Title'), Mage::getUrl('*/*/'));
        $this->_addBreadcrumb($breadCrumb, $breadCrumbTitle);

        $this->_addLeft(
            $this->getLayout()->createBlock('adminhtml/permissions_edituser')
                 ->setUserId($userId)
        );

        $this->_addContent(
            $this->getLayout()->createBlock('adminhtml/permissions_buttons')
                ->setTemplate('permissions/userinfo.phtml')
                ->setUserId($userId)
                ->assign('userData', Mage::getModel('permissions/users')->load($userId))
        );

        $this->renderLayout();
    }

    public function deleteUserAction()
    {
        $uid = $this->getRequest()->getParam('id', false);
        Mage::getModel("permissions/users")->setId($uid)->delete();

        $this->_redirect("adminhtml/permission_user");
    }

    public function saveUserAction()
    {
        $uid = $this->getRequest()->getParam('user_id', false);
        $user = Mage::getModel("permissions/users")
                ->setId($uid)
                ->setUsername($this->getRequest()->getParam('username', false))
                ->setFirstname($this->getRequest()->getParam('firstname', false))
                ->setLastname($this->getRequest()->getParam('lastname', false))
                ->setEmail(strtolower($this->getRequest()->getParam('email', false)))
                ->setPassword($this->getRequest()->getParam('password', false));

        if( !$user->userExists() ) {
            $user->save();
            Mage::getModel("permissions/users")
                ->setIds($this->getRequest()->getParam('roles', false))
                ->setUid($this->getRequest()->getParam('user_id', false))
                ->setFirstname($this->getRequest()->getParam('firstname', false))
                ->saveRel();

            $uid = $user->getId();
        } else {
            Mage::getSingleton('adminhtml/session')->addError('User with the same login or email aleady exists.');
        }
        $this->getResponse()->setRedirect(Mage::getUrl("*/*/edituser/id/{$uid}"));
    }

    public function deleteuserfromroleAction()
    {
        Mage::getModel("permissions/users")
            ->setUserId($this->getRequest()->getParam('user_id', false))
            ->deleteFromRole();
        echo json_encode(array('error' => 0, 'error_message' => 'test message'));
    }

    public function adduser2roleAction()
    {
        if( Mage::getModel("permissions/users")
                ->setRoleId($this->getRequest()->getParam('role_id', false))
                ->setUserId($this->getRequest()->getParam('user_id', false))
                ->roleUserExists() === true ) {
            echo json_encode(array('error' => 1, 'error_message' => __('This user already added to the role.')));
        } else {
            Mage::getModel("permissions/users")
                ->setRoleId($this->getRequest()->getParam('role_id', false))
                ->setUserId($this->getRequest()->getParam('user_id', false))
                ->setFirstname($this->getRequest()->getParam('firstname', false))
                ->add();
            echo json_encode(array('error' => 0, 'error_message' => 'test message'));
        }
    }
}