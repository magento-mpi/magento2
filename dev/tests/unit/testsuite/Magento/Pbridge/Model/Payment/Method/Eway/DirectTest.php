<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Pbridge\Model\Payment\Method\Eway;

use Magento\Framework\Object;

class DirectTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Direct|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_model;

    /**
     * setUp
     */
    protected function setUp()
    {
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_model = $helper->getObject(
            'Magento\Pbridge\Model\Payment\Method\Eway\Direct'
        );
    }

    /**
     * @dataProvider canVoidDataProvider
     */
    public function testCanVoid($invoiceCol, $expected)
    {
        $instance = $this->getMock(
            'Magento\Payment\Model\Info',
            ['getOrder', 'getInvoiceCollection', '__wakeup'],
            [],
            '',
            false
        );
        $instance->expects($this->once())->method('getOrder')->will($this->returnSelf());
        $instance->expects($this->once())->method('getInvoiceCollection')->will($this->returnValue($invoiceCol));
        $this->_model->setData('info_instance', $instance);
        $this->assertEquals($expected, $this->_model->canVoid(new \Magento\Framework\Object([])));
    }

    public function canVoidDataProvider()
    {
        return [
            [[], true], [[1, 2, 3, 4, 5], false]
        ];
    }
}
