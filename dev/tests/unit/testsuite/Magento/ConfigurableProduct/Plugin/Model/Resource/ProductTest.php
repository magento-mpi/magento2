<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ConfigurableProduct\Plugin\Model\Resource;

use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Catalog\Model\Product\Type;

class ProductTest extends \PHPUnit_Framework_TestCase
{
    public function testBeforeSaveConfigurable()
    {
        $subject = $this->getMock('Magento\Catalog\Model\Resource\Product', [], [], '', false);
        $object = $this->getMock('Magento\Catalog\Model\Product', ['getTypeId', 'getTypeInstance'], [], '', false);
        $type = $this->getMock(
            'Magento\ConfigurableProduct\Model\Product\Type\Configurable',
            ['getSetAttributes'],
            [],
            '',
            false
        );
        $type->expects($this->once())->method('getSetAttributes')->with($object);

        $object->expects($this->once())->method('getTypeId')->will($this->returnValue(Configurable::TYPE_CODE));
        $object->expects($this->once())->method('getTypeInstance')->will($this->returnValue($type));

        $product = new Product();
        $product->beforeSave(
            $subject,
            $object
        );
    }

    public function testBeforeSaveSimple()
    {
        $subject = $this->getMock('Magento\Catalog\Model\Resource\Product', [], [], '', false);
        $object = $this->getMock('Magento\Catalog\Model\Product', ['getTypeId', 'getTypeInstance'], [], '', false);
        $object->expects($this->once())->method('getTypeId')->will($this->returnValue(Type::TYPE_SIMPLE));
        $object->expects($this->never())->method('getTypeInstance');

        $product = new Product();
        $product->beforeSave(
            $subject,
            $object
        );
    }
}
