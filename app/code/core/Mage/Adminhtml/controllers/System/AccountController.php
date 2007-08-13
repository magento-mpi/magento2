<?php
/**
 * Adminhtml account controller
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Adminhtml_System_AccountController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->loadLayout('baseframe');
        $this->_setActiveMenu('system/account');

        $this->_addContent($this->getLayout()->createBlock('adminhtml/system_account_edit'));
        $this->renderLayout();
    }

    public function saveAction()
    {
        $userId = Mage::getSingleton('admin/session')->getUser()->getId();

        $user = Mage::getModel("permissions/users")
                ->setId($userId)
                ->setUsername($this->getRequest()->getParam('username', false))
                ->setFirstname($this->getRequest()->getParam('firstname', false))
                ->setLastname($this->getRequest()->getParam('lastname', false))
                ->setEmail(strtolower($this->getRequest()->getParam('email', false)))
                ->setPassword($this->getRequest()->getParam('password', false));

        if( !$user->userExists() ) {
            try {
                $user->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(__('Account successfully saved.'));
                $this->getResponse()->setRedirect(Mage::getUrl("*/*/"));
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(__('Error while saving account. Please try again later.'));
                $this->getResponse()->setRedirect(Mage::getUrl("*/*/"));
            }
        } else {
            Mage::getSingleton('adminhtml/session')->addError(__('User with the same User Name or Email aleady exists.'));
            $this->getResponse()->setRedirect(Mage::getUrl("*/*/"));
        }
    }
}