<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoAppArea adminhtml
 * @magentoDataFixture customerGroupDataFixture
 */
class Magento_Adminhtml_Controller_Customer_GroupTest extends Magento_Backend_Utility_Controller
{
    protected static $_customerGroupId;

    public static function customerGroupDataFixture()
    {
        /** @var \Magento\Customer\Model\Group $group */
        $group = Mage::getModel('\Magento\Customer\Model\Group');

        $groupData = array(
            'customer_group_code' => 'New Customer Group',
            'tax_class_id' => 3
        );
        $group->setData($groupData);
        $group->save();
        self::$_customerGroupId = $group->getId();
    }

    public function testNewAction()
    {
        $this->dispatch('backend/admin/customer_group/new');
        $responseBody = $this->getResponse()->getBody();
        $this->assertRegExp('/<h1 class\="title">\s*New Customer Group\s*<\/h1>/', $responseBody);
    }

    public function testDeleteActionExistingGroup()
    {
        $this->getRequest()->setParam('id', self::$_customerGroupId);
        $this->dispatch('backend/admin/customer_group/delete');

        /**
         * Check that success message is set
         */
        $this->assertSessionMessages(
            $this->equalTo(array('The customer group has been deleted.')), \Magento\Core\Model\Message::SUCCESS
        );
    }

    public function testDeleteActionNonExistingGroupId()
    {
        $this->getRequest()->setParam('id', 10000);
        $this->dispatch('backend/admin/customer_group/delete');

        /**
         * Check that error message is set
         */
        $this->assertSessionMessages(
            $this->equalTo(array('The customer group no longer exists.')), \Magento\Core\Model\Message::ERROR
        );
    }
}
