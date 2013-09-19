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
 * Test class for \Magento\AdminNotification\Block\ToolbarEntry
 */
namespace Magento\AdminNotification\Block;

class ToolbarEntryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Retrieve toolbar entry block instance
     *
     * @param int $unreadNotifications number of unread notifications
     * @return \Magento\AdminNotification\Block\ToolbarEntry
     */
    protected function _getBlockInstance($unreadNotifications)
    {
        $objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        // mock collection of unread notifications
        $notificationList = $this->getMock(
            'Magento\AdminNotification\Model\Resource\Inbox\Collection\Unread',
            array('getSize', 'setCurPage', 'setPageSize'),
            array(),
            '',
            false
        );
        $notificationList->expects($this->any())->method('getSize')->will($this->returnValue($unreadNotifications));

        $block = $objectManagerHelper->getObject(
            'Magento\AdminNotification\Block\ToolbarEntry',
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
