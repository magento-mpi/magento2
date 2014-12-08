<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Rma\Model\Resource;

class SetupTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Rma\Model\Resource\Setup
     */
    protected $rmaSetup;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $typeConfigMock;

    protected function setUp()
    {
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->typeConfigMock = $this->getMock('Magento\Catalog\Model\ProductTypes\ConfigInterface');
        $this->rmaSetup = $helper->getObject(
            'Magento\Rma\Model\Resource\Setup',
            ['productTypeConfig' => $this->typeConfigMock]
        );
    }

    public function testRefundableProducts()
    {
        $refundable = ['simple', 'simple2'];
        $isSet = ['simple', 'simple3'];
        $this->typeConfigMock->expects(
            $this->at(0)
        )->method(
            'filter'
        )->with(
            'refundable'
        )->will(
            $this->returnValue($refundable)
        );
        $this->typeConfigMock->expects(
            $this->at(1)
        )->method(
            'filter'
        )->with(
            'is_product_set'
        )->will(
            $this->returnValue($isSet)
        );
        $this->assertEquals(array_diff($refundable, $isSet), $this->rmaSetup->getRefundableProducts());
    }
}
