<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\PricePermissions\Controller\Adminhtml\Product\Initialization\Helper\Plugin\Handler;

class RecurringProfileTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RecurringProfile
     */
    protected $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $productMock;
    
    protected function setUp()
    {
        $this->productMock = $this->getMock('\Magento\Catalog\Model\Product',
            array('getOrigData', 'setRecurringProfile', '__wakeup'), array(), '', false
        );
        $this->model = new RecurringProfile();
    }

    public function testHandle()
    {
        $this->productMock->expects($this->once())
            ->method('getOrigData')
            ->with('recurring_profile')
            ->will($this->returnValue(array('some' => 'data')));

        $this->productMock->expects($this->once())->method('setRecurringProfile')->with(array('some' => 'data'));
        $this->model->handle($this->productMock);
    }
}
