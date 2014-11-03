<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogPermissions\Model\Indexer\Plugin;

class CustomerGroupV1Test extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Indexer\Model\IndexerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $indexerMock;

    /**
     * @var \Magento\CatalogPermissions\App\ConfigInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $appConfigMock;

    /**
     * @var \Magento\CatalogPermissions\Model\Indexer\Plugin\CustomerGroupV1
     */
    protected $customerGroupV1;

    /**
     * @var \Magento\Indexer\Model\IndexerRegistry|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $indexerRegistryMock;

    protected function setUp()
    {
        $this->indexerMock = $this->getMock(
            'Magento\Indexer\Model\Indexer',
            array('getId', 'load', 'invalidate'),
            array(),
            '',
            false
        );

        $this->appConfigMock = $this->getMock(
            'Magento\CatalogPermissions\App\Backend\Config',
            array('isEnabled'),
            array(),
            '',
            false
        );

        $this->indexerRegistryMock = $this->getMock('Magento\Indexer\Model\IndexerRegistry', ['get'], [], '', false);

        $this->customerGroupV1 = new CustomerGroupV1($this->indexerRegistryMock, $this->appConfigMock);
    }

    public function testAfterDeleteGroupIndexerOff()
    {
        $customerGroupService = $this->getMock(
            'Magento\Customer\Service\V1\CustomerGroupService',
            array(),
            array(),
            '',
            false
        );
        $this->appConfigMock->expects($this->once())->method('isEnabled')->will($this->returnValue(false));
        $this->indexerRegistryMock->expects($this->never())->method('get');
        $this->customerGroupV1->afterDeleteGroup($customerGroupService);
    }

    public function testAfterDeleteGroupIndexerOn()
    {
        $customerGroupService = $this->getMock(
            'Magento\Customer\Service\V1\CustomerGroupService',
            array(),
            array(),
            '',
            false
        );
        $this->appConfigMock->expects($this->once())->method('isEnabled')->will($this->returnValue(true));
        $this->indexerRegistryMock->expects($this->once())
            ->method('get')
            ->with(\Magento\CatalogPermissions\Model\Indexer\Category::INDEXER_ID)
            ->will($this->returnValue($this->indexerMock));
        $this->indexerMock->expects($this->once())->method('invalidate');

        $this->customerGroupV1->afterDeleteGroup($customerGroupService);
    }

    public function testAroundCreateGroupNoNeedInvalidating()
    {
        $customerGroupService = $this->getMock(
            'Magento\Customer\Service\V1\CustomerGroupService',
            array(),
            array(),
            '',
            false
        );

        $customerGroupMock = $this->getMock(
            'Magento\Customer\Service\V1\Data\CustomerGroup',
            array('getId'),
            array(),
            '',
            false
        );
        $customerGroupMock->expects($this->once())->method('getId')->will($this->returnValue(10));
        $this->appConfigMock->expects($this->never())->method('isEnabled')->will($this->returnValue(true));

        $proceedMock = function () {
            return 10;
        };

        $this->customerGroupV1->aroundCreateGroup($customerGroupService, $proceedMock, $customerGroupMock);
    }

    public function testAroundCreateGroupInvalidating()
    {
        $customerGroupService = $this->getMock(
            'Magento\Customer\Service\V1\CustomerGroupService',
            array(),
            array(),
            '',
            false
        );

        $customerGroupMock = $this->getMock(
            'Magento\Customer\Service\V1\Data\CustomerGroup',
            array('getId'),
            array(),
            '',
            false
        );
        $customerGroupMock->expects($this->once())->method('getId')->will($this->returnValue(0));
        $this->appConfigMock->expects($this->once())->method('isEnabled')->will($this->returnValue(true));
        $this->indexerMock->expects($this->once())->method('invalidate');
        $this->indexerRegistryMock->expects($this->once())
            ->method('get')
            ->with(\Magento\CatalogPermissions\Model\Indexer\Category::INDEXER_ID)
            ->will($this->returnValue($this->indexerMock));

        $proceedMock = function () {
            return 10;
        };

        $this->customerGroupV1->aroundCreateGroup($customerGroupService, $proceedMock, $customerGroupMock);
    }

    protected function prepareIndexer()
    {
        $this->indexerMock->expects($this->once())->method('getId')->will($this->returnValue(0));
        $this->indexerMock->expects(
            $this->once()
        )->method(
            'load'
        )->with(
            \Magento\CatalogPermissions\Model\Indexer\Category::INDEXER_ID
        )->will(
            $this->returnSelf()
        );
    }
}
