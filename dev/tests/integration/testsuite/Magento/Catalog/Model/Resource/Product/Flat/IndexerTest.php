<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Catalog_Model_Resource_Product_Flat_IndexerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Catalog_Model_Resource_Product_Flat_Indexer
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = Mage::getModel('Magento_Catalog_Model_Resource_Product_Flat_Indexer');
    }

    public function testGetAttributeCodes()
    {
        $actualResult = $this->_model->getAttributeCodes();
        $this->assertContains('name', $actualResult);
        $this->assertContains('price', $actualResult);
        $nameAttributeId = array_search('name', $actualResult);
        $priceAttributeId = array_search('price', $actualResult);
        $this->assertGreaterThan(0, $nameAttributeId, 'Id of the attribute "name" must be valid');
        $this->assertGreaterThan(0, $priceAttributeId, 'Id of the attribute "name" must be valid');
        $this->assertNotEquals($nameAttributeId, $priceAttributeId, 'Attribute ids must be different');
    }
}
