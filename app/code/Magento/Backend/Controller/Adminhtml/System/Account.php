<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Controller\Adminhtml\System;

use Magento\Backend\App\Action;

/**
 * Adminhtml account controller
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Account extends Action
{
    /**
     * @return void
     */
    public function indexAction()
    {
        $this->_title->add(__('My Account'));

        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }

    /**
     * Saving edited user information
     *
     * @return void
     */
    public function saveAction()
    {
        $userId = $this->_objectManager->get('Magento\Backend\Model\Auth\Session')->getUser()->getId();
        $password = (string)$this->getRequest()->getParam('password');
        $passwordConfirmation = (string)$this->getRequest()->getParam('password_confirmation');
        $interfaceLocale = (string)$this->getRequest()->getParam('interface_locale', false);

        /** @var $user \Magento\User\Model\User */
        $user = $this->_objectManager->create('Magento\User\Model\User')->load($userId);

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

        if ($this->_objectManager->get('Magento\Locale\Validator')->isValid($interfaceLocale)) {

            $user->setInterfaceLocale($interfaceLocale);
            $this->_objectManager->get('Magento\Backend\Model\Locale\Manager')
                ->switchBackendInterfaceLocale($interfaceLocale);
        }

        try {
            $user->save();
            $user->sendPasswordResetNotificationEmail();
            $this->messageManager->addSuccess(
                __('The account has been saved.')
            );
        } catch (\Magento\Core\Exception $e) {
            $this->messageManager->addMessages($e->getMessages());
        } catch (\Exception $e) {
            $this->messageManager->addError(
                __('An error occurred while saving account.')
            );
        }
        $this->getResponse()->setRedirect($this->getUrl("*/*/"));
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Adminhtml::myaccount');
    }
}
