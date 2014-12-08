<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\AdminGws\Model;

class ObserverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\AdminGws\Model\Observer
     */
    protected $_model;

    /**
     * @var \Magento\Framework\StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storeManager;

    /**
     * @var \Magento\Store\Model\Resource\Group\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storeGroups;

    /**
     * @var \Magento\Backend\Model\Auth\Session|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_backendAuthSession;

    /**
     * @var \Magento\Framework\Event\Observer
     */
    protected $_observer;

    /**
     * @var \Magento\Framework\Object
     */
    protected $_store;

    /**
     * @var \Magento\AdminGws\Model\Role
     */
    protected $_role;

    /**
     * @return void
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function setUp()
    {
        $websiteOne = $this->getMock('Magento\Store\Model\Website', ['__wakeup'], [], '', false);
        $websiteOne->setId(11);
        $websiteTwo = $this->getMock('Magento\Store\Model\Website', ['__wakeup'], [], '', false);
        $websiteTwo->setId(12);
        // Website with no store groups assigned to it
        $websiteIrrelevant = $this->getMock('Magento\Store\Model\Website', ['__wakeup'], [], '', false);
        $websiteIrrelevant->setId(13);

        $storeGroupOne = $this->getMock(
            'Magento\Store\Model\Group',
            ['getWebsite', '__wakeup'],
            [],
            '',
            false
        );
        $storeGroupOne->setId(21);
        $storeGroupOne->setWebsiteId(11);
        $storeGroupOne->expects($this->any())->method('getWebsite')->will($this->returnValue($websiteOne));
        $storeGroupTwo = $this->getMock(
            'Magento\Store\Model\Group',
            ['getWebsite', '__wakeup'],
            [],
            '',
            false
        );
        $storeGroupTwo->setId(22);
        $storeGroupTwo->setWebsiteId(12);
        $storeGroupTwo->expects($this->any())->method('getWebsite')->will($this->returnValue($websiteTwo));

        $storeOne = $this->getMock('Magento\Store\Model\Website', ['__wakeup'], [], '', false);
        $storeOne->setId(31);
        $storeOne->setGroupId(21);
        $storeTwo = $this->getMock('Magento\Store\Model\Website', ['__wakeup'], [], '', false);
        $storeTwo->setId(32);
        $storeTwo->setGroupId(21);
        // Store that belongs to unknown store group
        $storeIrrelevant = $this->getMock('Magento\Store\Model\Website', ['__wakeup'], [], '', false);
        $storeIrrelevant->setId(33);
        $storeIrrelevant->setGroupId(1);

        $this->_storeManager = $this->getMock('Magento\Framework\StoreManagerInterface');
        $this->_storeManager->expects(
            $this->any()
        )->method(
            'getWebsites'
        )->will(
            $this->returnValue([11 => $websiteOne, 12 => $websiteTwo, 13 => $websiteIrrelevant])
        );
        $this->_storeManager->expects(
            $this->any()
        )->method(
            'getStores'
        )->will(
            $this->returnValue([31 => $storeOne, 32 => $storeTwo, 33 => $storeIrrelevant])
        );

        $this->_storeGroups = $this->getMock(
            'Magento\Store\Model\Resource\Group\Collection',
            ['load'],
            [],
            '',
            false
        );
        $this->_storeGroups->addItem($storeGroupOne);
        $this->_storeGroups->addItem($storeGroupTwo);

        $this->_backendAuthSession = $this->getMock(
            'Magento\Backend\Model\Auth\Session',
            ['getUser'],
            [],
            '',
            false
        );

        $this->_store = new \Magento\Framework\Object();

        $this->_observer = $this->getMockBuilder(
            'Magento\Framework\Event\Observer'
        )->setMethods(
            ['getStore']
        )->disableOriginalConstructor()->getMock();
        $this->_observer->expects($this->any())->method('getStore')->will($this->returnValue($this->_store));

        $this->_role = $this->getMockBuilder(
            'Magento\AdminGws\Model\Role'
        )->setMethods(
            ['getStoreIds', 'setStoreIds']
        )->disableOriginalConstructor()->getMock();
        $this->_role->expects($this->any())->method('getStoreIds')->will($this->returnValue([1, 2, 3, 4, 5]));

        $objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_model = $objectManagerHelper->getObject(
            'Magento\AdminGws\Model\Observer',
            [
                'backendAuthSession' => $this->_backendAuthSession,
                'storeManager' => $this->_storeManager,
                'storeGroups' => $this->_storeGroups,
                'role' => $this->_role
            ]
        );
    }

    /**
     * @param array $fixtureRoleData
     * @param array $expectedRoleData
     * @dataProvider rolePermissionsDataProvider
     */
    public function testAddDataAfterRoleLoad(array $fixtureRoleData, array $expectedRoleData)
    {
        /** @var \Magento\Authorization\Model\Role|\PHPUnit_Framework_MockObject_MockObject $role */
        $role = $this->getMock('Magento\Authorization\Model\Role', ['__wakeup'], [], '', false);
        $role->setData($fixtureRoleData);

        $event = $this->getMock('Magento\Framework\Event', ['getObject'], [], '', false);
        $event->expects($this->once())->method('getObject')->will($this->returnValue($role));
        $observer = $this->getMock('Magento\Framework\Event\Observer', [], [], '', false);
        $observer->expects($this->once())->method('getEvent')->will($this->returnValue($event));

        $this->_backendAuthSession->expects($this->never())->method('getUser');

        $this->_model->addDataAfterRoleLoad($observer);

        $this->assertEquals($expectedRoleData, $role->getData());
    }

    /**
     * @param array $fixtureRoleData
     * @param array $expectedRoleData
     * @dataProvider rolePermissionsDataProvider
     */
    public function testRefreshRolePermissions(array $fixtureRoleData, array $expectedRoleData)
    {
        /** @var \Magento\Authorization\Model\Role|\PHPUnit_Framework_MockObject_MockObject $role */
        $role = $this->getMock('Magento\Authorization\Model\Role', ['__wakeup'], [], '', false);
        $role->setData($fixtureRoleData);

        $user = $this->getMock('Magento\User\Model\User', [], [], '', false);
        $user->expects($this->once())->method('getRole')->will($this->returnValue($role));

        $this->_backendAuthSession->expects($this->once())->method('getUser')->will($this->returnValue($user));

        $this->_model->refreshRolePermissions();

        $this->assertEquals($expectedRoleData, $role->getData());
    }

    public function rolePermissionsDataProvider()
    {
        return [
            'role scope: all' => [
                ['gws_is_all' => 1, 'gws_websites' => '12,13', 'gws_store_groups' => '21'],
                [
                    'gws_is_all' => true,
                    'gws_websites' => [11, 12, 13],
                    'gws_store_groups' => [21, 22],
                    'gws_stores' => [31, 32],
                    'gws_relevant_websites' => [11, 12]
                ],
            ],
            'role scope: custom & assigned store groups' => [
                ['gws_is_all' => 0, 'gws_websites' => '12,13', 'gws_store_groups' => '21'],
                [
                    'gws_is_all' => false,
                    'gws_websites' => [12, 13],
                    'gws_store_groups' => [21],
                    'gws_stores' => [31, 32],
                    'gws_relevant_websites' => [11]
                ],
            ],
            'role scope: custom & unassigned store groups' => [
                ['gws_is_all' => 0, 'gws_websites' => '11,13', 'gws_store_groups' => ''],
                [
                    'gws_is_all' => false,
                    'gws_websites' => [11, 13],
                    'gws_store_groups' => [21],
                    'gws_stores' => [31, 32],
                    'gws_relevant_websites' => [11]
                ],
            ]
        ];
    }

    public function testRefreshRolePermissionsInvalidUser()
    {
        $user = $this->getMock('stdClass', ['getRole'], [], '', false);
        $user->expects($this->never())->method('getRole');

        $this->_backendAuthSession->expects($this->once())->method('getUser')->will($this->returnValue($user));

        $this->_model->refreshRolePermissions();
    }

    public function testUpdateRoleStores()
    {
        $this->_store->setData('store_id', 1000);
        $this->_role->expects($this->once())->method('setStoreIds')->with($this->contains(1000));
        $this->_model->updateRoleStores($this->_observer);
    }
}
