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

namespace Magento\GroupedProduct\Model\Product\Type;

class GroupedTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Model\Product\Type
     */
    protected $_productType;

    protected function setUp()
    {
        $this->_productType = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get('Magento\Catalog\Model\Product\Type');
    }

    public function testFactory()
    {
        $product = new \Magento\Object;
        $product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_GROUPED);
        $type = $this->_productType->factory($product);
        $this->assertInstanceOf('\Magento\GroupedProduct\Model\Product\Type\Grouped', $type);
    }
}
