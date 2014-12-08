<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\PricePermissions\Controller\Adminhtml\Product\Initialization\Helper\Plugin\Handler;

class RecurringPaymentTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RecurringPayment
     */
    protected $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $productMock;

    protected function setUp()
    {
        $this->productMock = $this->getMock(
            '\Magento\Catalog\Model\Product',
            ['getOrigData', 'setRecurringPayment', '__wakeup'],
            [],
            '',
            false
        );
        $this->model = new RecurringPayment();
    }

    public function testHandle()
    {
        $this->productMock->expects(
            $this->once()
        )->method(
            'getOrigData'
        )->with(
            'recurring_payment'
        )->will(
            $this->returnValue(['some' => 'data'])
        );

        $this->productMock->expects($this->once())->method('setRecurringPayment')->with(['some' => 'data']);
        $this->model->handle($this->productMock);
    }
}
