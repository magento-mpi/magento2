<?php
/**
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */
class Magento_AdminNotification_Model_Resource_Inbox_Collection_CriticalTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\AdminNotification\Model\Resource\Inbox\Collection\Critical
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento\AdminNotification\Model\Resource\Inbox\Collection\Critical');
    }

    /**
     * @magentoDataFixture Magento/AdminNotification/_files/notifications.php
     */
    public function testCollectionContainsLastUnreadCriticalItem()
    {
        $items = array_values($this->_model->getItems());
        $this->assertEquals('Unread Critical 3', $items[0]->getTitle());
    }
}
