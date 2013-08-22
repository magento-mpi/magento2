<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_AdminNotification
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Magento_AdminNotification_Block_ToolbarEntry
 */
class Magento_AdminNotification_Block_ToolbarEntryTest extends PHPUnit_Framework_TestCase
{
    /**
     * Retrieve toolbar entry block instance
     *
     * @param int $unreadNotifications number of unread notifications
     * @return Magento_AdminNotification_Block_ToolbarEntry
     */
    protected function _getBlockInstance($unreadNotifications)
    {
        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
        // mock collection of unread notifications
        $notificationList = $this->getMock(
            'Magento_AdminNotification_Model_Resource_Inbox_Collection_Unread',
            array('getSize', 'setCurPage', 'setPageSize'),
            array(),
            '',
            false
        );
        $notificationList->expects($this->any())->method('getSize')->will($this->returnValue($unreadNotifications));

        $block = $objectManagerHelper->getObject(
            'Magento_AdminNotification_Block_ToolbarEntry',
            array(
                'notificationList' => $notificationList,
            )
        );

        return $block;
    }

    public function testGetUnreadNotificationCount()
    {
        $notificationsCount = 100;
        $block = $this->_getBlockInstance($notificationsCount);
        $this->assertEquals($notificationsCount, $block->getUnreadNotificationCount());
    }
}
