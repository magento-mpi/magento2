<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Search\Model\Indexer\Fulltext\Plugin;

class CustomerGroupTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Indexer\Model\IndexerInterface
     */
    protected $indexerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Customer\Model\Resource\Group
     */
    protected $subjectMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Search\Helper\Data
     */
    protected $helperMock;

    /**
     * @var CustomerGroup
     */
    protected $model;

    protected function setUp()
    {
        $this->subjectMock = $this->getMock('Magento\Customer\Model\Resource\Group', [], [], '', false);
        $this->helperMock = $this->getMock('Magento\Search\Helper\Data', [], [], '', false);
        $this->indexerMock = $this->getMockForAbstractClass(
            'Magento\Indexer\Model\IndexerInterface',
            [],
            '',
            false,
            false,
            true,
            ['getId', 'getState', '__wakeup']
        );
        $this->model = new CustomerGroup($this->indexerMock, $this->helperMock);
    }

    /**
     * @param bool $isThirdPartyEngineAvailable
     * @param bool $isObjectNew
     * @param bool $isTaxClassIdChanged
     * @param int $invalidateCounter
     * @return void
     * @dataProvider aroundSaveDataProvider
     */
    public function testAroundSave($isThirdPartyEngineAvailable, $isObjectNew, $isTaxClassIdChanged, $invalidateCounter)
    {
        $this->helperMock->expects($this->once())
            ->method('isThirdPartyEngineAvailable')
            ->will($this->returnValue($isThirdPartyEngineAvailable));

        $groupMock = $this->getMock(
            'Magento\Customer\Model\Group',
            ['dataHasChangedFor', 'isObjectNew', '__wakeup'],
            [],
            '',
            false
        );
        $groupMock->expects($this->any())->method('isObjectNew')->will($this->returnValue($isObjectNew));
        $groupMock->expects($this->any())
            ->method('dataHasChangedFor')
            ->with('tax_class_id')
            ->will($this->returnValue($isTaxClassIdChanged));

        $closureMock = function (\Magento\Customer\Model\Group $object) use ($groupMock) {
            $this->assertEquals($object, $groupMock);
            return $this->subjectMock;
        };

        $this->indexerMock->expects($this->exactly($invalidateCounter))->method('getId')->will($this->returnValue(1));
        $this->indexerMock->expects($this->exactly($invalidateCounter))->method('invalidate');

        $this->assertEquals(
            $this->subjectMock,
            $this->model->aroundSave($this->subjectMock, $closureMock, $groupMock)
        );
    }

    /**
     * @return array
     */
    public function aroundSaveDataProvider()
    {
        return [
            [false, false, false, 0],
            [false, false, true, 0],
            [false, true, false, 0],
            [false, true, true, 0],
            [true, false, false, 0],
            [true, false, true, 1],
            [true, true, false, 1],
            [true, true, true, 1],
        ];
    }
}
