<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\AdminGws\Model;

class RoleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\AdminGws\Model\Role
     */
    private $role;

    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    private $objectManagerHelper;

    protected function setUp()
    {
        $this->objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $storeManagerMock = $this->getMockBuilder('Magento\Store\Model\StoreManagerInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $storeManagerMock->expects($this->any())
            ->method('getWebsites')
            ->will($this->returnValue([1 => 'website']));
        $storeManagerMock->expects($this->any())
            ->method('getStores')
            ->will($this->returnValue([1 => 'store']));
        $storeManagerMock->expects($this->any())
            ->method('getGroups')
            ->will($this->returnValue([1 => 'group']));
        $this->role = $this->objectManagerHelper->getObject(
            'Magento\AdminGws\Model\Role',
            [
                'storeManager' => $storeManagerMock
            ]
        );
    }

    /**
     * @param $gwsRelevantWebsites
     * @param $gwsStores
     * @param $gwsStoreGroups
     * @param $gwsWebsites
     * @dataProvider adminRoleDataProvider
     */
    public function testSetAdminRole(
        $gwsRelevantWebsites,
        $gwsStores,
        $gwsStoreGroups,
        $gwsWebsites
    ) {
        $adminRole = $this->objectManagerHelper->getObject(
            'Magento\Authorization\Model\Role',
            [
                'data' => [
                    'gws_relevant_websites' => $gwsRelevantWebsites,
                    'gws_stores' => $gwsStores,
                    'gws_store_groups' => $gwsStoreGroups,
                    'gws_websites' => $gwsWebsites,
                ]
            ]
        );
        $this->role->setAdminRole($adminRole);
        $this->assertTrue(is_array($this->role->getStoreGroupIds()));
        $this->assertTrue(is_array($this->role->getWebsiteIds()));
        $this->assertTrue(is_array($this->role->getStoreIds()));
        $this->assertTrue(is_array($this->role->getRelevantWebsiteIds()));
    }

    public function adminRoleDataProvider()
    {
        return [
            [null, null, null, null],
            [
                [1, 2, 3],
                [1, 2, 3],
                [1, 2, 3],
                [1, 2, 3],
            ],
        ];
    }
}
