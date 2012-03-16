<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magento.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
     */
    public function testListWrongProductId()
    {
        $restResponse = $this->callGet($this->_getResourcePath('INVALID_ID'));
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_NOT_FOUND, $restResponse->getStatus());
        $body = $restResponse->getBody();
        $errors = $body['messages']['error'];
        $this->assertNotEmpty($errors);

        $expectedErrors = array(
            'Resource not found.'
        );

        $this->assertEquals(count($expectedErrors), count($errors));
        foreach ($errors as $error) {
            $this->assertContains($error['message'], $expectedErrors);
        }
    }

    /**
     * Test product categories list
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
     */
    public function testDelete()
    {
        $restResponse = $this->callDelete($this->_getResourcePath(1, 1));
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_FORBIDDEN, $restResponse->getStatus());
    }

    /**
     * Test firbidden delete for product category resource
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
