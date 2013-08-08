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
class Saas_Limitation_Mage_User_Controller_Adminhtml_UserTest extends Mage_Backend_Utility_Controller
{
    /**
     * @magentoConfigFixture limitations/admin_account 1
     */
    public function testIndexActionLimitedUsers()
    {
        $this->dispatch('backend/admin/user/index');
        $response = $this->getResponse()->getBody();
        $this->assertSelectEquals('#add.disabled', 'Add New User', 1, $response);
        // @codingStandardsIgnoreStart
        $this->assertContains('Sorry, you are using all the admin users your account allows. To add more, first delete an admin user or upgrade your service.', $response);
        // @codingStandardsIgnoreEnd
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoConfigFixture limitations/admin_account 1
     */
    public function testSaveActionLimitedUsers()
    {
        /** @var Mage_User_Model_Resource_User $userModel */
        $userModel = Mage::getResourceSingleton('Mage_User_Model_Resource_User');
        $usersCountBefore = $userModel->countAll();
        $this->getRequest()->setPost(array(
            'username' => 'test',
            'email' => "test@example.com",
            'firstname' => 'First',
            'lastname' => 'Last',
            'password' => 'password1',
            'password_confirmation' => 'password1',
        ));
        $this->dispatch('backend/admin/user/save');
        $this->assertSessionMessages(
            // @codingStandardsIgnoreStart
            $this->equalTo(array('Sorry, you are using all the admin users your account allows. To add more, first delete an admin user or upgrade your service.')),
            // @codingStandardsIgnoreEnd
            Magento_Core_Model_Message::ERROR
        );
        $this->assertRedirect($this->stringContains('backend/admin/user/edit/'));
        $this->assertSame($usersCountBefore, $userModel->countAll());
    }
}
