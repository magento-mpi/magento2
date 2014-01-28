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
        $types = array(
            'simple' => array('name' => 'simple', 'custom_attributes' => array('refundable' => 'true')),
            'simple2' => array('name' => 'simple2', 'custom_attributes' => array('refundable' => 'some_value')),
            'some_product_name' => array('name' => 'some_product_name', 'custom_attributes' => array()),
        );
        $this->configMock->expects($this->once())->method('getAll')->will($this->returnValue($types));
        $this->assertEquals(array('simple'), $this->refundableList->getItem());
    }
}
