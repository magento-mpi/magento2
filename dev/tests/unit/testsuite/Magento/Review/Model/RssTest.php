<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Review\Model;

use Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

class RssTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Review\Model\Rss
     */
    protected $rss;

    /**
     * @var ObjectManagerHelper
     */
    protected $objectManagerHelper;

    /**
     * @var \Magento\Framework\Event\ManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $managerInterface;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $reviewFactory;

    protected function setUp()
    {
        $this->managerInterface = $this->getMock('Magento\Framework\Event\ManagerInterface');
        $this->reviewFactory = $this->getMock('Magento\Review\Model\ReviewFactory', ['create']);

        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->rss = $this->objectManagerHelper->getObject(
            'Magento\Review\Model\Rss',
            [
                'eventManager' => $this->managerInterface,
                'reviewFactory' => $this->reviewFactory
            ]
        );
    }

    public function testGetProductCollection()
    {
        $reviewModel = $this->getMock(
            'Magento\Review\Model\Review',
            [
                '__wakeUp',
                'getProductCollection'
            ],
            [],
            '',
            false
        );
        $productCollection = $this->getMock(
            'Magento\Review\Model\Resource\Review\Product\Collection',
            [
                'addStatusFilter',
                'addAttributeToSelect',
                'setDateOrder'
            ],
            [],
            '',
            false
        );
        $reviewModel->expects($this->once())->method('getProductCollection')
            ->will($this->returnValue($productCollection));
        $this->reviewFactory->expects($this->once())->method('create')->will($this->returnValue($reviewModel));
        $productCollection->expects($this->once())->method('addStatusFilter')->will($this->returnSelf());
        $productCollection->expects($this->once())->method('addAttributeToSelect')->will($this->returnSelf());
        $productCollection->expects($this->once())->method('setDateOrder')->will($this->returnSelf());
        $this->managerInterface->expects($this->once())->method('dispatch')->will($this->returnSelf());
        $this->assertEquals($productCollection, $this->rss->getProductCollection());
    }
}
