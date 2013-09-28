<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Adminhtml\System;

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
        $user = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\User\Model\User')->load($userId);
        $oldPassword = $user->getPassword();

        $password = uniqid('123q');
        $request = $this->getRequest();
        $request->setParam('username', $user->getUsername())->setParam('email', $user->getEmail())
            ->setParam('firstname', $user->getFirstname())->setParam('lastname', $user->getLastname())
            ->setParam('password', $password)->setParam('password_confirmation', $password);
        $this->dispatch('backend/admin/system_account/save');

        /** @var $user \Magento\User\Model\User */
        $user = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\User\Model\User')->load($userId);
        $this->assertNotEquals($oldPassword, $user->getPassword());
        $this->assertTrue(
            \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Helper\Data')
                ->validateHash($password, $user->getPassword())
        );
    }
}
