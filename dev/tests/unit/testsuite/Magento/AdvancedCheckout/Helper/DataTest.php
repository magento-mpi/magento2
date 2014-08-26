<?php
/**
 * Created by PhpStorm.
 * User: ilagno
 * Date: 22.08.2014
 * Time: 17:46
 */

namespace Magento\AdvancedCheckout\Helper;


class DataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\AdvancedCheckout\Helper\Data
     */
    protected $helper;

    protected function setUp()
    {
        $objManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->helper = $objManagerHelper->getObject('Magento\AdvancedCheckout\Helper\Data');
    }

    public function testGetDeleteFailedItemPostJson()
    {
        $itemMock = $this->getMock('\Magento\Sales\Model\Quote\Item', ['getSku', '__wakeUp'], [], '', false);
        $itemMock->expects($this->once())->method('getSku')->will($this->returnValue('sku'));
        $expected = json_encode(array('action' => 'http://url.com', 'data' => 'sku'));
        $this->assertEquals($expected, $this->helper->getDeleteFailedItemPostJson('http://url.com', $itemMock));
    }
}
