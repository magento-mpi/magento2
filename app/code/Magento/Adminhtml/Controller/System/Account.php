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
 * Adminhtml account controller
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_Adminhtml_Controller_System_Account extends Magento_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->_title(__('My Account'));

        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Saving edited user information
     */
    public function saveAction()
    {
        $userId = $this->_objectManager->get('Magento_Backend_Model_Auth_Session')->getUser()->getId();
        $password = (string)$this->getRequest()->getParam('password');
        $passwordConfirmation = (string)$this->getRequest()->getParam('password_confirmation');
        $interfaceLocale = (string)$this->getRequest()->getParam('interface_locale', false);

        /** @var $user Magento_User_Model_User */
        $user = $this->_objectManager->create('Magento_User_Model_User')->load($userId);

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

        if ($this->_objectManager->get('Magento_Core_Model_Locale_Validator')->isValid($interfaceLocale)) {

            $user->setInterfaceLocale($interfaceLocale);
            $this->_objectManager->get('Magento_Backend_Model_Locale_Manager')
                ->switchBackendInterfaceLocale($interfaceLocale);
        }

        try {
            $user->save();
            $user->sendPasswordResetNotificationEmail();
            $this->_getSession()->addSuccess(
                __('The account has been saved.')
            );
        } catch (Magento_Core_Exception $e) {
            $this->_getSession()->addMessages($e->getMessages());
        } catch (Exception $e) {
            $this->_getSession()->addError(
                __('An error occurred while saving account.')
            );
        }
        $this->getResponse()->setRedirect($this->getUrl("*/*/"));
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Adminhtml::myaccount');
    }
}
