<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Rma\Model\Rma\Source;

class AbstractSourceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Rma\Model\Item\Attribute\Source\StatusFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $statusFactoryMock;

    /**
     * @var \Magento\Core\Helper\Data|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $coreDataMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $attrOptionCollectionFactoryMock;

    /**
     * @var \Magento\Eav\Model\Resource\Entity\Attribute\OptionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $attrOptionFactoryMock;

    /**
     * @var Status
     */
    protected $status;

    public function setUp()
    {
        $this->statusFactoryMock = $this->getMock(
            'Magento\Rma\Model\Item\Attribute\Source\StatusFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->coreDataMock = $this->getMock(
            'Magento\Core\Helper\Data',
            [],
            [],
            '',
            false
        );
        $this->attrOptionCollectionFactoryMock = $this->getMock(
            'Magento\Eav\Model\Resource\Entity\Attribute\Option\CollectionFactory',
            [],
            [],
            '',
            false
        );
        $this->attrOptionFactoryMock = $this->getMock(
            'Magento\Eav\Model\Resource\Entity\Attribute\OptionFactory',
            [],
            [],
            '',
            false
        );
        $this->status = new Status(
            $this->coreDataMock,
            $this->attrOptionCollectionFactoryMock,
            $this->attrOptionFactoryMock,
            $this->statusFactoryMock
        );
    }

    /**
     * @dataProvider getAllOptionsDataProvider
     * @param bool $withLabels
     * @param array $expected
     */
    public function testGetAllOptions($withLabels, $expected)
    {
        $this->assertEquals($expected, $this->status->getAllOptions($withLabels));
    }

    public function testGetAllOptionsForGrid()
    {
        $expected = [
            Status::STATE_PENDING => 'Pending',
            Status::STATE_AUTHORIZED=> 'Authorized',
            Status::STATE_PARTIAL_AUTHORIZED => 'Partially Authorized',
            Status::STATE_RECEIVED => 'Return Received',
            Status::STATE_RECEIVED_ON_ITEM => 'Return Partially Received' ,
            Status::STATE_APPROVED_ON_ITEM => 'Partially Approved',
            Status::STATE_REJECTED_ON_ITEM => 'Partially Rejected',
            Status::STATE_CLOSED => 'Closed',
            Status::STATE_PROCESSED_CLOSED => 'Processed and Closed'
        ];
        $this->assertEquals($expected, $this->status->getAllOptionsForGrid());

    }

    public function getAllOptionsDataProvider()
    {
        return [
            [
                true,
                [
                    ['label' => 'Pending', 'value' => Status::STATE_PENDING],
                    ['label' => 'Authorized', 'value' => Status::STATE_AUTHORIZED],
                    ['label' => 'Partially Authorized', 'value' => Status::STATE_PARTIAL_AUTHORIZED],
                    ['label' => 'Return Received', 'value' => Status::STATE_RECEIVED],
                    ['label' => 'Return Partially Received', 'value' => Status::STATE_RECEIVED_ON_ITEM],
                    ['label' => 'Partially Approved', 'value' => Status::STATE_APPROVED_ON_ITEM],
                    ['label' => 'Partially Rejected', 'value' => Status::STATE_REJECTED_ON_ITEM],
                    ['label' => 'Closed', 'value' => Status::STATE_CLOSED],
                    ['label' => 'Processed and Closed', 'value' => Status::STATE_PROCESSED_CLOSED],
                ]
            ],
            [
                false,
                [
                    Status::STATE_PENDING,
                    Status::STATE_AUTHORIZED,
                    Status::STATE_PARTIAL_AUTHORIZED,
                    Status::STATE_RECEIVED,
                    Status::STATE_RECEIVED_ON_ITEM,
                    Status::STATE_APPROVED_ON_ITEM,
                    Status::STATE_REJECTED_ON_ITEM,
                    Status::STATE_CLOSED,
                    Status::STATE_PROCESSED_CLOSED
                ]
            ]
        ];
    }
}
