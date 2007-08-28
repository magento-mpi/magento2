<?php
/**
 * Adminhtml permissions users controller
 *
 * @package Mage
 * @subpackage  Adminhtml
 * @copyright Varien (c) 2007 (http://www.varien.com)
 * @license http://www.opensource.org/licenses/osl-3.0.php
 * @author Alexander Stadnitski <alexander@varien.com>
 */
class Mage_Adminhtml_Permissions_UserController extends Mage_Adminhtml_Controller_Action
{


    protected function _initAction()
    {
        $this->loadLayout('baseframe')
            ->_setActiveMenu('system/acl')
            ->_addBreadcrumb(__('System'), __('System'))
            ->_addBreadcrumb(__('Permissions'), __('Permissions'))
            ->_addBreadcrumb(__('Users'), __('Users'))
        ;
        return $this;
    }

    public function indexAction()
    {
        $this->_initAction()
            ->_addContent($this->getLayout()->createBlock('adminhtml/permissions_user'))
            ->renderLayout();
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        $id = $this->getRequest()->getParam('user_id');
        $model = Mage::getModel('permissions/user');

        if ($id) {
            $model->load($id);
            if (! $model->getId()) {
                Mage::getSingleton('adminhtml/session')->addError(__('This user no longer exists'));
                $this->_redirect('*/*/');
                return;
            }
        }

        // set entered data if was error when we do save
        $data = Mage::getSingleton('adminhtml/session')->getUserData(true);
        print_r($data);
        if (! empty($data)) {
            $model->setData($data);
        }

        Mage::register('permissions_user', $model);

        $this->_initAction()
            ->_addBreadcrumb($id ? __('Edit User') : __('New User'), $id ? __('Edit User') : __('New User'))
            ->_addContent($this->getLayout()->createBlock('adminhtml/permissions_user_edit')->setData('action', Mage::getUrl('*/permissions_user/save')))
            ->_addLeft($this->getLayout()->createBlock('adminhtml/permissions_user_edit_tabs'))
            ->renderLayout();
    }

    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            $model = Mage::getModel('permissions/user');
//            if ($id = $this->getRequest()->getParam('page_id')) {
//                $model->load($id);
//                if ($id != $model->getId()) {
//                    Mage::getSingleton('adminhtml/session')->addError('The page you are trying to save no longer exists');
//                    Mage::getSingleton('adminhtml/session')->setPageData($data);
//                    $this->_redirect('*/*/edit', array('page_id' => $this->getRequest()->getParam('page_id')));
//                    return;
//                }
//            }
            $model->setData($data);
            try {
                $model->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(__('User was saved succesfully'));
                Mage::getSingleton('adminhtml/session')->setUserData(false);
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setUserData($data);
                $this->_redirect('*/*/edit', array('user_id' => $this->getRequest()->getParam('user_id')));
                return;
            }
        }
        $this->_redirect('*/*/');
    }

    public function deleteAction()
    {
        if ($id = $this->getRequest()->getParam('user_id')) {
            try {
                $model = Mage::getModel('permissions/user');
                $model->setId($id);
                $model->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(__('User was deleted succesfully'));
                $this->_redirect('*/*/');
                return;
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('user_id' => $this->getRequest()->getParam('user_id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(__('Unable to find a user to delete'));
        $this->_redirect('*/*/');
    }

//    public function userGridAction()
//    {
//        $this->getResponse()->setBody($this->getLayout()->createBlock('adminhtml/permissions_grid_user')->toHtml());
//    }
//
//    public function deleteuserfromroleAction()
//    {
//        Mage::getModel("permissions/users")
//            ->setUserId($this->getRequest()->getParam('user_id', false))
//            ->deleteFromRole();
//        echo json_encode(array('error' => 0, 'error_message' => 'test message'));
//    }
//
//    public function adduser2roleAction()
//    {
//        if( Mage::getModel("permissions/users")
//                ->setRoleId($this->getRequest()->getParam('role_id', false))
//                ->setUserId($this->getRequest()->getParam('user_id', false))
//                ->roleUserExists() === true ) {
//            echo json_encode(array('error' => 1, 'error_message' => __('This user already added to the role.')));
//        } else {
//            Mage::getModel("permissions/users")
//                ->setRoleId($this->getRequest()->getParam('role_id', false))
//                ->setUserId($this->getRequest()->getParam('user_id', false))
//                ->setFirstname($this->getRequest()->getParam('firstname', false))
//                ->add();
//            echo json_encode(array('error' => 0, 'error_message' => 'test message'));
//        }
//    }

    protected function _isAllowed()
    {
	    return Mage::getSingleton('admin/session')->isAllowed('system/acl/users');
    }

}
