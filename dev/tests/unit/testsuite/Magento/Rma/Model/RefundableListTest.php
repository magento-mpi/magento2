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
        $this->configMock = $this->getMock('Magento\Catalog\Model\ProductTypes\Config', array(), array(), '', false);

        $this->refundableList = new \Magento\Rma\Model\RefundableList($this->configMock);
    }

    public function testGetItem()
    {
        $type = array(
            array('name' => 'grouped', 'custom_attributes' => array('refundable' => false)),
            array('name' => 'simple', 'custom_attributes' => array('refundable' => true))
        );
        $this->configMock->expects($this->once())->method('getAll')->will($this->returnValue($type));
        $this->assertEquals(array('simple'), $this->refundableList->getItem());

    }
}
