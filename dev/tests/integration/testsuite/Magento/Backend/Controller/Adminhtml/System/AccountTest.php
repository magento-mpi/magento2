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
     * @dataProvider saveDataProvider
     * @magentoDbIsolation enabled
     */
    public function testSaveAction($password, $passwordConfirmation, $isPasswordChanged)
    {
        $userId = $this->_session->getUser()->getId();
        /** @var $user \Magento\User\Model\User */
        $user = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\User\Model\User'
        )->load(
            $userId
        );
        $oldPassword = $user->getPassword();

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
            $passwordConfirmation
        );
        $this->dispatch('backend/admin/system_account/save');

        /** @var $user \Magento\User\Model\User */
        $user = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\User\Model\User'
        )->load(
            $userId
        );

        if ($isPasswordChanged) {
            $this->assertNotEquals($oldPassword, $user->getPassword());
            $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
            /** @var $encryptor \Magento\Framework\Encryption\EncryptorInterface */
            $encryptor = $objectManager->get('Magento\Framework\Encryption\EncryptorInterface');
            $this->assertTrue($encryptor->validateHash($password, $user->getPassword()));

        } else {
            $this->assertEquals($oldPassword, $user->getPassword());
        }
    }

    public function saveDataProvider()
    {
        $password = uniqid('123q');
        return array(
            array($password, $password, true),
            array($password, '', false),
            array($password, $password . '123', false),
            array('', '', false),
            array('', $password, false)
        );
    }
}
