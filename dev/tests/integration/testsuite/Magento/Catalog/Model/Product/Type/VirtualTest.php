<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Product\Type;

class VirtualTest extends \PHPUnit_Framework_TestCase
{
    public function testIsVirtual()
    {
        /** @var $model \Magento\Catalog\Model\Product\Type\Virtual */
        $model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Catalog\Model\Product\Type\Virtual'
        );
        $product = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Catalog\Model\Product'
        );
        $this->assertTrue($model->isVirtual($product));
    }
}
