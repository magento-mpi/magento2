<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\SalesArchive\Block\Adminhtml\Sales\Order;

class GridTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $size = 5;
        $authorization = $this->getMockForAbstractClass('Magento\Framework\AuthorizationInterface');
        $authorization->expects($this->once())
            ->method('isAllowed')
            ->will($this->returnValue(true));

        $buttonList = $this->getMock('Magento\Backend\Block\Widget\Button\ButtonList', [], [], '', false);

        $orderCollection = $this->getMock(
            'Magento\SalesArchive\Model\Resource\Order\Collection',
            [],
            [],
            '',
            false
        );
        $orderCollection->expects($this->once())
            ->method('getSize')
            ->will($this->returnValue($size));

        $params = [
            'authorization' => $authorization,
            'buttonList' => $buttonList,
            'orderCollection' => $orderCollection,
        ];

        $expectedButtonData =  [
            'label' => 'Go to Archive (5 orders)',
            'onclick' => 'setLocation(\'\')',
            'class' => 'go',
        ];
        $buttonList->expects($this->at(1))->method('add')->with('go_to_archive', $expectedButtonData, 0, 0, 'toolbar');
        $objectManager->getObject('Magento\SalesArchive\Block\Adminhtml\Sales\Order\Grid', $params);
    }
}
