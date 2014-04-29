<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Controller\Adminhtml\System;

/**
 * @magentoAppArea adminhtml
 */
class AccountTest extends \Magento\Backend\Utility\Controller
{
    /**
     * @magentoDbIsolation enabled
     */
    public function testSaveAction()
    {
        $userId = $this->_session->getUser()->getId();
        /** @var $user \Magento\User\Model\User */
        $user = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\User\Model\User'
        )->load(
            $userId
        );
        $oldPassword = $user->getPassword();

        $password = uniqid('123q');
        $request = $this->getRequest();
        $request->setParam(
            'username',
            $user->getUsername()
        )->setParam(
            'email',
            $user->getEmail()
        )->setParam(
            'firstname',
            $user->getFirstname()
        )->setParam(
            'lastname',
            $user->getLastname()
        )->setParam(
            'password',
            $password
        )->setParam(
            'password_confirmation',
            $password
        );
        $this->dispatch('backend/admin/system_account/save');

        /** @var $user \Magento\User\Model\User */
        $user = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\User\Model\User'
        )->load(
            $userId
        );
        $this->assertNotEquals($oldPassword, $user->getPassword());


        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->assertTrue(
            $objectManager->get('Magento\Framework\Encryption\EncryptorInterface')
                ->validateHash($password, $user->getPassword())
        );
    }
}
