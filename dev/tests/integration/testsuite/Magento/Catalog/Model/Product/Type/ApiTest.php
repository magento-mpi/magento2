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

class Magento_Catalog_Model_Product_Type_ApiTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param string $class
     * @dataProvider itemsDataProvider
     */
    public function testItems($class)
    {
        /** @var $model \Magento\Catalog\Model\Product\Type\Api */
        $model = Mage::getModel($class);
        $result = $model->items();
        $this->assertInternalType('array', $result);
        $this->assertNotEmpty($result);
        foreach ($result as $item) {
            $this->assertArrayHasKey('type', $item);
            $this->assertArrayHasKey('label', $item);
        }
    }

    public function itemsDataProvider()
    {
        return array(
            array('Magento\Catalog\Model\Product\Type\Api'),
            array('Magento\Catalog\Model\Product\Type\Api\V2'), // a dummy class, doesn't require separate test suite
        );
    }
}
