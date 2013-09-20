<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model\Product\Type;

class VirtualTest extends \PHPUnit_Framework_TestCase
{
    public function testIsVirtual()
    {
        /** @var $model \Magento\Catalog\Model\Product\Type\Virtual */
        $model = \Mage::getModel('Magento\Catalog\Model\Product\Type\Virtual');
        $product = \Mage::getModel('Magento\Catalog\Model\Product');
        $this->assertTrue($model->isVirtual($product));
    }
}
