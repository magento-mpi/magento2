<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoAppArea adminhtml
 */
class Mage_Adminhtml_Customer_GroupControllerTest extends Mage_Backend_Utility_Controller
{
    public function testNewAction()
    {
        $this->dispatch('backend/admin/customer_group/new');
        $responseBody = $this->getResponse()->getBody();
        $this->assertRegExp('/<h1 class\="title">\s*New Customer Group\s*<\/h1>/', $responseBody);
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDataFixture Mage/Adminhtml/controllers/_files/customer_group_sample.php
     */
    public function testDeleteActionExistingGroup()
    {
        /** @var $registry Mage_Core_Model_Registry */
        $registry = Mage::getObjectManager()->get('Mage_Core_Model_Registry');
        /** @var $group Mage_Customer_Model_Group */
        $group = $registry->registry('_fixture/Mage_Customer_Model_Group');

        $this->getRequest()->setParam('id', $group->getId());
        $this->dispatch('backend/admin/customer_group/delete');

        /**
         * Check that success message is set
         */
        $this->assertSessionMessages(
            $this->equalTo(array('The customer group has been deleted.')), Mage_Core_Model_Message::SUCCESS
        );
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testDeleteActionNonExistingGroupId()
    {
        $this->getRequest()->setParam('id', 10000);
        $this->dispatch('backend/admin/customer_group/delete');

        /**
         * Check that error message is set
         */
        $this->assertSessionMessages(
            $this->equalTo(array('The customer group no longer exists.')), Mage_Core_Model_Message::ERROR
        );
    }
}
