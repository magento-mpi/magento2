<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml account controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_System_AccountController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->_title($this->__('My Account'));

        $this->loadLayout();
        $this->_addContent($this->getLayout()->createBlock('Mage_Adminhtml_Block_System_Account_Edit'));
        $this->renderLayout();
    }

    /**
     * Saving edited user information
     */
    public function saveAction()
    {
        $userId = $this->_objectManager->get('Mage_Backend_Model_Auth_Session')->getUser()->getId();
        $password = (string)$this->getRequest()->getParam('password');
        $passwordConfirmation = (string)$this->getRequest()->getParam('password_confirmation');
        $interfaceLocale = (string)$this->getRequest()->getParam('interface_locale', false);

        /** @var $user Mage_User_Model_User */
        $user = $this->_objectManager->create('Mage_User_Model_User')->load($userId);

        $user->setId($userId)
            ->setUsername($this->getRequest()->getParam('username', false))
            ->setFirstname($this->getRequest()->getParam('firstname', false))
            ->setLastname($this->getRequest()->getParam('lastname', false))
            ->setEmail(strtolower($this->getRequest()->getParam('email', false)));

        if ($password !== '') {
            $user->setPassword($password);
        }
        if ($passwordConfirmation !== '') {
            $user->setPasswordConfirmation($passwordConfirmation);
        }

        if ($this->_objectManager->get('Mage_Core_Model_Locale_Validator')->isValid($interfaceLocale)) {

            $user->setInterfaceLocale($interfaceLocale);
            $this->_objectManager->get('Mage_Backend_Model_Locale_Manager')
                ->switchBackendInterfaceLocale($interfaceLocale);
        }

        try {
            $user->save();
            $user->sendPasswordResetNotificationEmail($this->_objectManager->create('Mage_Core_Model_Email_Info'));
            $this->_getSession()->addSuccess(
                $this->__('The account has been saved.')
            );
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addMessages($e->getMessages());
        } catch (Exception $e) {
            $this->_getSession()->addError(
                $this->__('An error occurred while saving account.')
            );
        }
        $this->getResponse()->setRedirect($this->getUrl("*/*/"));
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('Mage_Core_Model_Authorization')->isAllowed('Mage_Adminhtml::myaccount');
    }
}
