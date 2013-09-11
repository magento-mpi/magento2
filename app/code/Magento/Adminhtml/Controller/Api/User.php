<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Adminhtml\Controller\Api;

class User extends \Magento\Adminhtml\Controller\Action
{

    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('Magento_Api::system_legacy_api_users')
            ->_addBreadcrumb(__('Web Services'), __('Web Services'))
            ->_addBreadcrumb(__('Permissions'), __('Permissions'))
            ->_addBreadcrumb(__('Users'), __('Users'))
        ;
        return $this;
    }

    public function indexAction()
    {
        $this->_title(__('Users'));

        $this->_initAction()
            ->renderLayout();
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        $this->_title(__('Users'));

        $id = $this->getRequest()->getParam('user_id');
        $model = \Mage::getModel('Magento\Api\Model\User');

        if ($id) {
            $model->load($id);
            if (! $model->getId()) {
                \Mage::getSingleton('Magento\Adminhtml\Model\Session')->addError(__('This user no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
        }

        $this->_title($model->getId() ? $model->getName() : __('New User'));

        // Restore previously entered form data from session
        $data = \Mage::getSingleton('Magento\Adminhtml\Model\Session')->getUserData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        \Mage::register('api_user', $model);

        $this->_initAction()
            ->_addBreadcrumb($id ? __('Edit User') : __('New User'), $id ? __('Edit User') : __('New User'))
            ->_addContent($this->getLayout()
                ->createBlock('Magento\Adminhtml\Block\Api\User\Edit')
                ->setData('action', $this->getUrl('*/api_user/save')))
            ->_addLeft($this->getLayout()->createBlock('Magento\Adminhtml\Block\Api\User\Edit\Tabs'));

        $this->_addJs($this->getLayout()
            ->createBlock('Magento\Adminhtml\Block\Template')
            ->setTemplate('api/user_roles_grid_js.phtml')
        );
        $this->renderLayout();
    }

    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            $id = $this->getRequest()->getPost('user_id', false);
            $model = \Mage::getModel('Magento\Api\Model\User')->load($id);
            if (!$model->getId() && $id) {
                \Mage::getSingleton('Magento\Adminhtml\Model\Session')->addError(__('This user no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
            $model->setData($data);
            try {
                $model->save();
                if ( $uRoles = $this->getRequest()->getParam('roles', false) ) {
                    /*parse_str($uRoles, $uRoles);
                    $uRoles = array_keys($uRoles);*/
                    if ( 1 == sizeof($uRoles) ) {
                        $model->setRoleIds($uRoles)
                            ->setRoleUserId($model->getUserId())
                            ->saveRelations();
                    } else if ( sizeof($uRoles) > 1 ) {
                        //@FIXME: stupid fix of previous multi-roles logic.
                        //@TODO:  make proper DB upgrade in the future revisions.
                        $rs = array();
                        $rs[0] = $uRoles[0];
                        $model->setRoleIds( $rs )->setRoleUserId( $model->getUserId() )->saveRelations();
                    }
                }
                \Mage::getSingleton('Magento\Adminhtml\Model\Session')->addSuccess(__('You saved the user.'));
                \Mage::getSingleton('Magento\Adminhtml\Model\Session')->setUserData(false);
                $this->_redirect('*/*/edit', array('user_id' => $model->getUserId()));
                return;
            } catch (\Exception $e) {
                \Mage::getSingleton('Magento\Adminhtml\Model\Session')->addError($e->getMessage());
                \Mage::getSingleton('Magento\Adminhtml\Model\Session')->setUserData($data);
                $this->_redirect('*/*/edit', array('user_id' => $model->getUserId()));
                return;
            }
        }
        $this->_redirect('*/*/');
    }

    public function deleteAction()
    {
        if ($id = $this->getRequest()->getParam('user_id')) {

            try {
                $model = \Mage::getModel('Magento\Api\Model\User')->load($id);
                $model->delete();
                \Mage::getSingleton('Magento\Adminhtml\Model\Session')->addSuccess(__('You deleted the user.'));
                $this->_redirect('*/*/');
                return;
            }
            catch (\Exception $e) {
                \Mage::getSingleton('Magento\Adminhtml\Model\Session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('user_id' => $this->getRequest()->getParam('user_id')));
                return;
            }
        }
        \Mage::getSingleton('Magento\Adminhtml\Model\Session')->addError(__('We can\'t find a user to delete.'));
        $this->_redirect('*/*/');
    }

    public function rolesGridAction()
    {
        $id = $this->getRequest()->getParam('user_id');
        $model = \Mage::getModel('Magento\Api\Model\User');

        if ($id) {
            $model->load($id);
        }

        \Mage::register('api_user', $model);
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('Magento\Adminhtml\Block\Api\User\Edit\Tab\Roles')->toHtml()
        );
    }

    public function roleGridAction()
    {
        $this->loadLayout(false);
        $this->renderLayout();
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Api::users');
    }

}
