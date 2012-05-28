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

/**
 * Test product categories resource
 *
 * @category    Magento
 * @package     Magento_Test
 * @author      Magento Api Team <api-team@magento.com>
 */

class Api2_Catalog_Products_Categories_GuestTest extends Magento_Test_Webservice_Rest_Guest
{
    /**
     * Delete fixtures
     */
    protected function tearDown()
    {
        self::deleteFixture('product_simple', true);
        parent::tearDown();
    }

    /**
     * Delete store fixture
     */
    public static function tearDownAfterClass()
    {
        self::deleteFixture('store', true);
        self::deleteFixture('store_group', true);
        self::deleteFixture('website', true);
        self::deleteFixture('category', true);
        parent::tearDownAfterClass();
    }

    /**
     * Test product categories list
     *
     * @resourceOperation product_category::multiget
     */
    public function testList()
    {
        $categoryData = require dirname(__FILE__) . '/_fixtures/Backend/ProductCategoryData.php';
        $categoryCreatedData = require dirname(__FILE__) . '/_fixtures/Backend/ProductCategoryCreatedData.php';

        $fixturesDir = realpath(dirname(__FILE__) . '/../../../../../fixtures');

        /* @var $product Mage_Catalog_Model_Product */
        $product = require $fixturesDir . '/Catalog/Product.php';
        $product->setStoreId(0)
            ->setWebsiteIds(array(Mage::app()->getWebsite()->getId()))
            ->setCategoryIds($categoryData['category_id'] . ',' . $categoryCreatedData['category_id'])
            ->save();
        self::setFixture('product_simple', $product);

        $restResponse = $this->callGet($this->_getResourcePath($product->getId()));
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());
        $responseData = $restResponse->getBody();
        $this->assertNotEmpty($responseData);
        $originalData = $product->getCategoryIds();
        $this->assertEquals(count($responseData), count($originalData));
        $this->assertContains($categoryData['category_id'], $product->getCategoryIds());
        $this->assertContains($categoryCreatedData['category_id'], $product->getCategoryIds());
    }

    /**
     * Test product categories resource list for nonexistent product
     *
     * @resourceOperation product_category::multiget
     */
    public function testListWrongProductId()
    {
        $restResponse = $this->callGet($this->_getResourcePath('INVALID_ID'));
        $this->_checkErrorMessagesInResponse($restResponse, 'Resource not found.',
            Mage_Api2_Model_Server::HTTP_NOT_FOUND);
    }

    /**
     * Test product categories list
     *
     * @resourceOperation product_category::multiget
     */
    public function testListWithInactiveCategory()
    {
        $categoryData = require dirname(__FILE__) . '/_fixtures/Backend/ProductCategoryData.php';
        $categoryCreatedData = require dirname(__FILE__) . '/_fixtures/Backend/ProductCategoryCreatedData.php';

        $fixturesDir = realpath(dirname(__FILE__) . '/../../../../../fixtures');

        /* @var $product Mage_Catalog_Model_Product */
        $product = require $fixturesDir . '/Catalog/Product.php';
        $product->setStoreId(0)
            ->setWebsiteIds(array(Mage::app()->getWebsite()->getId()))
            ->setCategoryIds($categoryData['category_id'] . ',' . $categoryCreatedData['category_id'])
            ->save();
        self::setFixture('product_simple', $product);

        $category = self::getFixture('category');
        $category->setStoreId(0)->setData('is_active', 0)->save();

        $restResponse = $this->callGet($this->_getResourcePath($product->getId()));

        $category->setStoreId(0)->setData('is_active', 1)->save();

        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());
        $responseData = $restResponse->getBody();
        $this->assertNotEmpty($responseData);

        $categoryIds = array();
        foreach($responseData as $categoryData) {
            $categoryIds[] = $categoryData['category_id'];
        }
        $this->assertCount(1, $categoryIds);
        $this->assertContains($categoryData['category_id'], $categoryIds);
        $this->assertNotContains($categoryCreatedData['category_id'], $categoryIds);
    }

    /**
     * Test firbidden delete for product category resource
     *
     * @resourceOperation product_category::delete
     */
    public function testDelete()
    {
        $restResponse = $this->callDelete($this->_getResourcePath(1, 1));
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_FORBIDDEN, $restResponse->getStatus());
    }

    /**
     * Test firbidden delete for product category resource
     *
     * @resourceOperation product_category::create
     */
    public function testCreate()
    {
        $restResponse = $this->callPost($this->_getResourcePath(1), array());
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_FORBIDDEN, $restResponse->getStatus());
    }

    /**
     * Create path to resource
     *
     * @param int $productId
     * @param int $categoryId
     * @return string
     */
    protected function _getResourcePath($productId, $categoryId = null)
    {
        $path = "products/{$productId}/categories";
        if ($categoryId) {
            $path .= "/{$categoryId}";
        }
        return $path;
    }
}
