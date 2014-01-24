<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Rma\Model;

class RefundableListTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Rma\Model\RefundableList
     */
    protected $refundableList;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $configMock;

    protected function setUp()
    {
        $this->configMock = $this->getMock('Magento\Catalog\Model\ProductTypes\ConfigInterface');
        $this->refundableList = new \Magento\Rma\Model\RefundableList($this->configMock);
    }

    public function testGetItem()
    {
        $type[0]['name'] = 'simple';
        $type[0]['custom_attributes']['refundable'] = true;
        $type[1]['name'] = 'grouped';
        $type[1]['custom_attributes']['refundable'] = 'some_value';
        $type[2]['name'] = 'some_product_name';
        $this->configMock->expects($this->once())->method('getAll')->will($this->returnValue($type));
        $this->assertEquals(array('simple'), $this->refundableList->getItem());
    }
}
