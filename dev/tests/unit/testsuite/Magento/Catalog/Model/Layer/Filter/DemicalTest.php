<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Layer\Filter;

class DecimalTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $attributeModel = $this->getMock(
            'Magento\Catalog\Model\Resource\Eav\Attribute',
            array('getAttributeCode', '__wakeup'),
            array(),
            '',
            false
        );
        $attributeModel->expects($this->once())
            ->method('getAttributeCode')
            ->will($this->returnValue('price1'));

        $filterItemFactory = $this->getMock('Magento\Catalog\Model\Layer\Filter\ItemFactory');
        $storeManager = $this->getMock('Magento\Store\Model\StoreManagerInterface');
        $layer = $this->getMock('Magento\Catalog\Model\Layer', array(), array(), '', false);
        $filterDecimalFactory = $this->getMock(
            'Magento\Catalog\Model\Resource\Layer\Filter\DecimalFactory',
            array('create')
        );
        $filterDecimalFactory->expects($this->once())
            ->method('create')
            ->will(
                $this->returnValue(
                    $this->getMock('Magento\Catalog\Model\Resource\Layer\Filter\Decimal', array(), array(), '', false)
                )
            );
        $instance = new \Magento\Catalog\Model\Layer\Filter\Decimal(
            $filterItemFactory,
            $storeManager,
            $layer,
            $filterDecimalFactory,
            array('attribute_model' => $attributeModel)
        );
        $this->assertSame('price1', $instance->getRequestVar());
    }
}
