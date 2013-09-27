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

class Magento_Catalog_Model_Resource_Product_CollectionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Catalog_Model_Resource_Product_Collection
     */
    protected $_collection;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->_collection = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Catalog_Model_Resource_Product_Collection');
    }

    /**
     * @dataProvider setOrderDataProvider
     */
    public function testSetOrder($order, $expectedOrder)
    {
        $this->_collection->setOrder($order);
        $this->_collection->load(); // perform real SQL query

        $selectOrder = $this->_collection->getSelect()->getPart(Zend_Db_Select::ORDER);
        foreach ($expectedOrder as $field) {
            $orderBy = array_shift($selectOrder);
            $this->assertArrayHasKey(0, $orderBy);
            $this->assertTrue(false !== strpos($orderBy[0], $field),
                'Ordering by same column more than once is restricted by multiple RDBMS requirements.'
            );
        }
    }

    public function setOrderDataProvider()
    {
        return array(
            array(array('sku', 'sku'), array('sku')),
            array(array('sku', 'name', 'sku'), array('name', 'sku')),
        );
    }

    /**
     * @magentoDataFixture Magento/Catalog/Model/Resource/_files/url_rewrites.php
     * @magentoConfigFixture current_store catalog/seo/product_use_categories 1
     */
    public function testAddUrlRewrite()
    {
        $this->_collection->addUrlRewrite(3);
        $expectedResult = array(
            'category-1/url-key.html',
            'category-1/url-key-1.html',
            'category-1/url-key-2.html',
            'category-1/url-key-5.html',
            'category-1/url-key-1000.html',
            'category-1/url-key-999.html',
            'category-1/url-key-asdf.html',
        );
        $this->assertEquals($expectedResult, $this->_collection->getColumnValues('request_path'));
    }
}
