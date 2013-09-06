<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoAppArea adminhtml
 */
class Magento_Adminhtml_System_AccountTest extends Magento_Backend_Utility_Controller
{
    /**
     * @magentoDbIsolation enabled
     */
    public function testSaveAction()
    {
        $userId = $this->_session->getUser()->getId();
        /** @var $user Magento_User_Model_User */
        $user = Mage::getModel('Magento_User_Model_User')->load($userId);
        $oldPassword = $user->getPassword();

        $password = uniqid('123q');
        $request = $this->getRequest();
        $request->setParam('username', $user->getUsername())->setParam('email', $user->getEmail())
            ->setParam('firstname', $user->getFirstname())->setParam('lastname', $user->getLastname())
            ->setParam('password', $password)->setParam('password_confirmation', $password);
        $this->dispatch('backend/admin/system_account/save');

        /** @var $user Magento_User_Model_User */
        $user = Mage::getModel('Magento_User_Model_User')->load($userId);
        $this->assertNotEquals($oldPassword, $user->getPassword());
        $this->assertTrue(Mage::helper('Magento_Core_Helper_Data')->validateHash($password, $user->getPassword()));
    }
}
