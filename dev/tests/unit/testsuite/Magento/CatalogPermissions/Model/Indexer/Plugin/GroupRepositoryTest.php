<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogPermissions\Model\Indexer\Plugin;

class GroupRepositoryTest extends \PHPUnit_Framework_TestCase
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
     * @var \Magento\CatalogPermissions\Model\Indexer\Plugin\GroupRepository
     */
    protected $groupRepositoryPlugin;

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

        $this->groupRepositoryPlugin = new \Magento\CatalogPermissions\Model\Indexer\Plugin\GroupRepository($this->indexerMock, $this->appConfigMock);
    }

    public function testAfterDeleteGroupIndexerOff()
    {
        $customerGroupService = $this->getMock(
            'Magento\Customer\Model\Resource\GroupRepository',
            array(),
            array(),
            '',
            false
        );
        $this->appConfigMock->expects($this->once())->method('isEnabled')->will($this->returnValue(false));
        $this->indexerMock->expects($this->never())->method('getId');
        $this->groupRepositoryPlugin->afterDelete($customerGroupService);
    }

    public function testAfterDeleteIndexerOn()
    {
        $customerGroupService = $this->getMock(
            'Magento\Customer\Model\Resource\GroupRepository',
            array(),
            array(),
            '',
            false
        );
        $this->appConfigMock->expects($this->once())->method('isEnabled')->will($this->returnValue(true));
        $this->prepareIndexer();
        $this->indexerMock->expects($this->once())->method('invalidate');
        $this->groupRepositoryPlugin->afterDelete($customerGroupService);
    }

    public function testAfterSaveNoNeedInvalidating()
    {
        $customerGroupService = $this->getMock(
            'Magento\Customer\Model\Resource\GroupRepository',
            array(),
            array(),
            '',
            false
        );

        $customerGroupMock = $this->getMock(
            'Magento\Customer\Model\Data\Group',
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

        $this->groupRepositoryPlugin->aroundSave($customerGroupService, $proceedMock, $customerGroupMock);
    }

    public function testAfterSaveInvalidating()
    {
        $customerGroupService = $this->getMock(
            'Magento\Customer\Model\Resource\GroupRepository',
            array(),
            array(),
            '',
            false
        );

        $customerGroupMock = $this->getMock(
            'Magento\Customer\Model\Data\Group',
            array('getId'),
            array(),
            '',
            false
        );
        $customerGroupMock->expects($this->once())->method('getId')->will($this->returnValue(0));
        $this->appConfigMock->expects($this->once())->method('isEnabled')->will($this->returnValue(true));
        $this->prepareIndexer();
        $this->indexerMock->expects($this->once())->method('invalidate');

        $proceedMock = function () {
            return 10;
        };

        $this->groupRepositoryPlugin->aroundSave($customerGroupService, $proceedMock, $customerGroupMock);
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
