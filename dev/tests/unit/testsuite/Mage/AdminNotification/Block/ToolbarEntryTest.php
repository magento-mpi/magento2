<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_AdminNotification
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Mage_AdminNotification_Block_ToolbarEntry
 */
class Mage_AdminNotification_Block_ToolbarEntryTest extends PHPUnit_Framework_TestCase
{
    /**
     * Retrieve toolbar entry block instance
     *
     * @param int $unreadNotifications number of unread notifications
     * @return Mage_AdminNotification_Block_ToolbarEntry
     */
    protected function _getBlockInstance($unreadNotifications)
    {
        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
        // mock collection of unread notifications
        $notificationList = $this->getMock(
            'Mage_AdminNotification_Model_Resource_Inbox_UnreadNotificationCollection',
            array('getSize', 'setCurPage', 'setPageSize'),
            array(),
            '',
            false
        );
        $notificationList->expects($this->any())->method('getSize')->will($this->returnValue($unreadNotifications));

        $block = $objectManagerHelper->getObject(
            'Mage_AdminNotification_Block_ToolbarEntry',
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
