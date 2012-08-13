<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Eav_Model_Entity_AbstractTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Eav_Model_Entity
     */
    protected $_entity;
    /**
     * @var Mage_Eav_Model_Entity_Attribute
     */
    protected $_attribute;

    /**
     * @var Mage_Catalog_Model_Product
     */
    protected $_product;

    protected function setUp()
    {
        $this->_entity = new Mage_Eav_Model_Entity();
        $this->_entity->setType(Mage_Catalog_Model_Product::ENTITY);
        $this->_attribute = new Mage_Eav_Model_Entity_Attribute();
        $this->_attribute->setAttributeCode('sku');
        $this->_attribute->setEntity($this->_entity);
        $this->_product = new Mage_Catalog_Model_Product();
    }

    protected function tearDown()
    {
        $this->_entity = null;
        $this->_attribute = null;
    }

    /**
     * @magentoDataFixture Mage/Catalog/_files/product_simple.php
     */
    public function testGetLastSimilarAttributeValueIncrementNewProduct()
    {
        $this->_product->load(1);
        $this->assertEquals(0,
            $this->_entity->getLastSimilarAttributeValueIncrement($this->_attribute, $this->_product));
    }

    /**
     * @magentoDataFixture Mage/Catalog/_files/product_simple.php
     * @magentoDataFixture Mage/Catalog/_files/product_simple_duplicated.php
     */
    public function testGetLastSimilarAttributeValueIncrementDuplicatedProduct()
    {
        $this->_product->load(1);
        $this->assertEquals(1,
            $this->_entity->getLastSimilarAttributeValueIncrement($this->_attribute, $this->_product));
    }
}
