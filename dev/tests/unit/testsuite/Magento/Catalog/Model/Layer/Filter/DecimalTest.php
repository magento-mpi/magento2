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
    public function testConstructorRequestVarIsOverwrittenCorrectlyInParent()
    {
        $attributeModel = $this->getMock(
            'Magento\Catalog\Model\Resource\Eav\Attribute',
            array('getAttributeCode', '__wakeup'),
            array(),
            '',
            false
        );
        $attributeModel->expects($this->once())->method('getAttributeCode')->will($this->returnValue('price1'));

        $filterDecimalFactory = $this->getMock(
            'Magento\Catalog\Model\Resource\Layer\Filter\DecimalFactory',
            array('create')
        );
        $filterDecimalFactory->expects($this->once())->method('create')->will(
            $this->returnValue(
                $this->getMock('Magento\Catalog\Model\Resource\Layer\Filter\Decimal', array(), array(), '', false)
            )
        );
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);

        $instance = $objectManager->getObject(
            'Magento\Catalog\Model\Layer\Filter\Decimal',
            array(
                'filterDecimalFactory' => $filterDecimalFactory,
                'data' => array('attribute_model' => $attributeModel)
            )
        );
        $this->assertSame('price1', $instance->getRequestVar());
    }
}
