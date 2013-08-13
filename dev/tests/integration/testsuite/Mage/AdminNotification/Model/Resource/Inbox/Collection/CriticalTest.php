<?php
/**
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */
class Mage_AdminNotification_Model_Resource_Inbox_Collection_CriticalTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_AdminNotification_Model_Resource_Inbox_Collection_Critical
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = Magento_Test_Helper_Bootstrap::getObjectManager()
            ->create('Mage_AdminNotification_Model_Resource_Inbox_Collection_Critical');
    }

    /**
     * @magentoDataFixture Mage/AdminNotification/_files/notifications.php
     */
    public function testCollectionContainsLastUnreadCriticalItem()
    {
        $items = array_values($this->_model->getItems());
        $this->assertEquals('Unread Critical 3', $items[0]->getTitle());
    }
}
