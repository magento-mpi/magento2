<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Catalog
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test product website resource
 *
 * @category    Magento
 * @package     Magento_Test
 * @author      Magento Api Team <api-team@magento.com>
 */
class Webapi_Catalog_Product_Website_AdminTest extends Magento_Test_Webservice_Rest_Admin
{
    /**
     * Delete fixtures
     */
    protected function tearDown()
    {
        self::deleteFixture('product', true);
        foreach ($this->getFixture('websitesAssignedToProduct') as $websiteAssignedToProduct) {
            $this->callModelDelete($websiteAssignedToProduct, true);
        }
        foreach ($this->getFixture('stores') as $store) {
            $this->callModelDelete($store, true);
        }
        foreach ($this->getFixture('storeGroups') as $storeGroup) {
            $this->callModelDelete($storeGroup, true);
        }
        foreach ($this->getFixture('categories') as $category) {
            $this->callModelDelete($category, true);
        }
        foreach ($this->getFixture('websitesNotAssignedToProduct') as $websiteNotAssignedToProduct) {
            $this->callModelDelete($websiteNotAssignedToProduct, true);
        }

        parent::tearDown();
    }

    /**
     * Test of retrieve list of asigned websites (GET method)
     *
     * @magentoDataFixture testsuite/Webapi/Catalog/Product/Website/_fixture/websites.php
     * @resourceOperation product_website::multiget
     */
    public function testRetrieveAsignedWebsites()
    {
        /* @var $product Mage_Catalog_Model_Product */
        $product = self::getFixture('product');

        $restResponse = $this->callGet('products/' . $product->getId() . '/websites');
        $this->assertEquals(Mage_Webapi_Controller_Front_Rest::HTTP_OK, $restResponse->getStatus());

        /* @var $product Mage_Catalog_Model_Product */
        $websiteIds = $product->getWebsiteIds();
        $product = Mage::getModel('Mage_Catalog_Model_Product')->load($product->getId());
        foreach (self::getFixture('websitesAssignedToProduct') as $website) {
            $this->assertContains($website->getId(), $websiteIds);
        }
    }

    /**
     * Test of a website assignment to a product (with "Stores Data Copying") (POST method)
     *
     * @magentoDataFixture testsuite/Webapi/Catalog/Product/Website/_fixture/websites.php
     * @resourceOperation product_website::create
     */
    public function testAssignWebsiteToProductWithCopyToStores()
    {
        /* @var $websiteNotAssignedToProduct Mage_Core_Model_Website */
        $websiteNotAssignedToProduct = array_shift(self::getFixture('websitesNotAssignedToProduct'));
        /* @var $websiteAssignedToProduct Mage_Core_Model_Website */
        $websiteAssignedToProduct = array_shift(self::getFixture('websitesAssignedToProduct'));
        /* @var $product Mage_Catalog_Model_Product */
        $product = self::getFixture('product');

        $assignedStoredDataId = array_shift($websiteAssignedToProduct->getStoreIds());
        $notAssignedStoredDataId = array_shift($websiteNotAssignedToProduct->getStoreIds());
        $websitesData = array(
            'website_id' => $websiteNotAssignedToProduct->getId(),
            'copy_to_stores' => array(
                array(
                    'store_from' => $assignedStoredDataId,
                    'store_to' => $notAssignedStoredDataId,
                )
            )
        );
        $restResponse = $this->callPost('products/' . $product->getId() . '/websites', $websitesData);
        $this->assertEquals(Mage_Webapi_Controller_Front_Rest::HTTP_OK, $restResponse->getStatus());

        // Check website
        /* @var $product Mage_Catalog_Model_Product */
        $product = Mage::getModel('Mage_Catalog_Model_Product')->load($product->getId());
        $this->assertContains($websiteNotAssignedToProduct->getId(), $product->getWebsiteIds());

        // Check stores
        $existStoreData = Mage::getModel('Mage_Catalog_Model_Product')
            ->setStoreId($assignedStoredDataId)
            ->load($product->getId())
            ->toArray();
        unset($existStoreData['store_id']);
        unset($existStoreData['stock_item']['store_id']);

        $newStoreData = Mage::getModel('Mage_Catalog_Model_Product')
            ->setStoreId($notAssignedStoredDataId)
            ->load($product->getId())
            ->toArray();
        unset($newStoreData['store_id']);
        unset($newStoreData['stock_item']['store_id']);

        $this->assertEquals($existStoreData, $newStoreData);
    }

    /**
     * Test of a website assignment to an unavailable product (POST method)
     *
     * @magentoDataFixture testsuite/Webapi/Catalog/Product/Website/_fixture/websites.php
     * @resourceOperation product_website::create
     */
    public function testWebsiteAssignmentToUnavailableProduct()
    {
        /* @var $website Mage_Core_Model_Website */
        $websiteNotAssignedToProduct = array_shift(self::getFixture('websitesNotAssignedToProduct'));

        $websitesData = array(
            'website_id' => $websiteNotAssignedToProduct->getId()
        );
        $restResponse = $this->callPost('products/-1/websites', $websitesData);
        $this->assertEquals(Mage_Webapi_Controller_Front_Rest::HTTP_NOT_FOUND, $restResponse->getStatus());

        $responseData = $restResponse->getBody();
        $this->assertArrayHasKey('error', $responseData['messages']);
        $this->assertEquals($responseData['messages']['error'][0]['message'], 'Product #-1 not found.');
    }

    /**
     * Test invalid website id in request body (POST method)
     *
     * @magentoDataFixture testsuite/Webapi/Catalog/Product/Website/_fixture/websites.php
     * @resourceOperation product_website::create
     */
    public function testInvalidWebsiteIdInRequestBody()
    {
        /* @var $product Mage_Catalog_Model_Product */
        $product = self::getFixture('product');

        $websitesData = array(
            'website_id' => 'invalid_id'
        );
        $restResponse = $this->callPost('products/' . $product->getId() . '/websites', $websitesData);
        $this->assertEquals(Mage_Webapi_Controller_Front_Rest::HTTP_BAD_REQUEST, $restResponse->getStatus());

        $responseData = $restResponse->getBody();
        $this->assertArrayHasKey('error', $responseData['messages']);
        $this->assertEquals($responseData['messages']['error'][0]['message'],
            'Invalid value for "website_id" in request.');
    }

    /**
     * Test of a unavailable website assignment to a product (POST method)
     *
     * @magentoDataFixture testsuite/Webapi/Catalog/Product/Website/_fixture/websites.php
     * @resourceOperation product_website::create
     */
    public function testUnavailableWebsiteAssignmentToProduct()
    {
        /* @var $product Mage_Catalog_Model_Product */
        $product = self::getFixture('product');

        $websitesData = array(
            'website_id' => -1
        );
        $restResponse = $this->callPost('products/' . $product->getId() . '/websites', $websitesData);
        $this->assertEquals(Mage_Webapi_Controller_Front_Rest::HTTP_BAD_REQUEST, $restResponse->getStatus());

        $responseData = $restResponse->getBody();
        $this->assertArrayHasKey('error', $responseData['messages']);
        $this->assertEquals($responseData['messages']['error'][0]['message'], 'Website #-1 not found.');
    }

    /**
     * Test of an attempt to assign a website that is already assigned to the product (POST method)
     *
     * @magentoDataFixture testsuite/Webapi/Catalog/Product/Website/_fixture/websites.php
     * @resourceOperation product_website::create
     */
    public function testAssignWebsiteThatIsAlreadyAssignedToProduct()
    {
        /* @var $websiteAssignedToProduct Mage_Core_Model_Website */
        $websiteAssignedToProduct = array_shift(self::getFixture('websitesAssignedToProduct'));
        /* @var $product Mage_Catalog_Model_Product */
        $product = self::getFixture('product');

        $websitesData = array(
            'website_id' => $websiteAssignedToProduct->getId()
        );
        $restResponse = $this->callPost('products/' . $product->getId() . '/websites', $websitesData);
        $this->assertEquals(Mage_Webapi_Controller_Front_Rest::HTTP_BAD_REQUEST, $restResponse->getStatus());

        $responseData = $restResponse->getBody();
        $this->assertArrayHasKey('error', $responseData['messages']);
        $this->assertEquals(
            $responseData['messages']['error'][0]['message'],
            sprintf(
                'Product #%d is already assigned to website #%d',
                $product->getId(),
                $websiteAssignedToProduct->getId()
            )
        );
    }

    /**
     * Test invalid store ids in copy to stores data (POST method)
     *
     * @magentoDataFixture testsuite/Webapi/Catalog/Product/Website/_fixture/websites.php
     * @resourceOperation product_website::create
     */
    public function testInvalidStoreIdsInCopyToStoresData()
    {
        /* @var $websiteNotAssignedToProduct Mage_Core_Model_Website */
        $websiteNotAssignedToProduct = array_shift(self::getFixture('websitesNotAssignedToProduct'));
        /* @var $product Mage_Catalog_Model_Product */
        $product = self::getFixture('product');

        $websitesData = array(
            'website_id' => $websiteNotAssignedToProduct->getId(),
            'copy_to_stores' => array(
                array(
                    'store_from' => 'invalid_id',
                    'store_to' => 'invalid_id',
                )
            )
        );
        $restResponse = $this->callPost('products/' . $product->getId() . '/websites', $websitesData);
        $this->assertEquals(Mage_Webapi_Controller_Front_Rest::HTTP_BAD_REQUEST, $restResponse->getStatus());

        $responseData = $restResponse->getBody();
        $this->assertArrayHasKey('error', $responseData['messages']);
        $this->assertEquals(
            $responseData['messages']['error'][0]['message'],
            sprintf(
                'Invalid value for "store_from" for the website with ID #%d.',
                $websiteNotAssignedToProduct->getId()
            )
        );
        $this->assertEquals(
            $responseData['messages']['error'][1]['message'],
            sprintf(
                'Invalid value for "store_to" for the website with ID #%d.',
                $websiteNotAssignedToProduct->getId()
            )
        );
    }

    /**
     * Test use unavailable stores in copy to stores data (POST method)
     *
     * @magentoDataFixture testsuite/Webapi/Catalog/Product/Website/_fixture/websites.php
     * @resourceOperation product_website::create
     */
    public function testUseUnavailableStoresInCopyToStoresData()
    {
        /* @var $websiteNotAssignedToProduct Mage_Core_Model_Website */
        $websiteNotAssignedToProduct = array_shift(self::getFixture('websitesNotAssignedToProduct'));
        /* @var $product Mage_Catalog_Model_Product */
        $product = self::getFixture('product');

        $websitesData = array(
            'website_id' => $websiteNotAssignedToProduct->getId(),
            'copy_to_stores' => array(
                array(
                    'store_from' => -1,
                    'store_to' => -1,
                )
            )
        );
        $restResponse = $this->callPost('products/' . $product->getId() . '/websites', $websitesData);
        $this->assertEquals(Mage_Webapi_Controller_Front_Rest::HTTP_BAD_REQUEST, $restResponse->getStatus());

        $responseData = $restResponse->getBody();
        $this->assertArrayHasKey('error', $responseData['messages']);
        $this->assertEquals(
            $responseData['messages']['error'][0]['message'],
            sprintf(
                'Store not found #-1 for website #%d.',
                $websiteNotAssignedToProduct->getId()
            )
        );
        $this->assertEquals(
            $responseData['messages']['error'][1]['message'],
            sprintf(
                'Store not found #-1 for website #%d.',
                $websiteNotAssignedToProduct->getId()
            )
        );
    }

    /**
     * Test use invalid stores associations in copy to stores data (POST method)
     *
     * @magentoDataFixture testsuite/Webapi/Catalog/Product/Website/_fixture/websites.php
     * @resourceOperation product_website::create
     */
    public function testUseInvalidStoresAssociationsInCopyToStoresData()
    {
        /* @var $websiteNotAssignedToProduct Mage_Core_Model_Website */
        $websiteNotAssignedToProduct = array_shift(self::getFixture('websitesNotAssignedToProduct'));
        /* @var $websiteAssignedToProduct Mage_Core_Model_Website */
        $websiteAssignedToProduct = array_shift(self::getFixture('websitesAssignedToProduct'));
        /* @var $product Mage_Catalog_Model_Product */
        $product = self::getFixture('product');

        $assignedStoredDataId = array_shift($websiteAssignedToProduct->getStoreIds());
        $notAssignedStoredDataId = array_shift($websiteNotAssignedToProduct->getStoreIds());
        $websitesData = array(
            'website_id' => $websiteNotAssignedToProduct->getId(),
            'copy_to_stores' => array(
                array(
                    'store_from' => $notAssignedStoredDataId, // wrong store from which we will copy the information
                    'store_to' => $assignedStoredDataId, // wrong store to which we will copy the information
                )
            )
        );
        $restResponse = $this->callPost('products/' . $product->getId() . '/websites', $websitesData);
        $this->assertEquals(Mage_Webapi_Controller_Front_Rest::HTTP_BAD_REQUEST, $restResponse->getStatus());

        $responseData = $restResponse->getBody();
        $this->assertArrayHasKey('error', $responseData['messages']);
        $this->assertEquals(
            $responseData['messages']['error'][0]['message'],
            sprintf(
                'Store #%d from which we will copy the information does not belong to the product #%d being edited.',
                $notAssignedStoredDataId,
                $product->getId()
            )
        );
        $this->assertEquals(
            $responseData['messages']['error'][1]['message'],
            sprintf(
                'Store #%d to which we will copy the information does not belong to the website #%d being added.',
                $assignedStoredDataId,
                $websiteNotAssignedToProduct->getId()
            )
        );
    }

    /**
     * Test of a website multi assignment to a product (with copy to stores) (POST method)
     *
     * @magentoDataFixture testsuite/Webapi/Catalog/Product/Website/_fixture/websites.php
     * @resourceOperation product_website::multicreate
     */
    public function testMultiAssignWebsiteToProduct()
    {
        $websitesNotAssignedToProduct = self::getFixture('websitesNotAssignedToProduct');
        /* @var $websiteAssignedToProduct Mage_Core_Model_Website */
        $websiteAssignedToProduct = array_shift(self::getFixture('websitesAssignedToProduct'));
        $assignedStoredDataId = array_shift($websiteAssignedToProduct->getStoreIds());
        /* @var $product Mage_Catalog_Model_Product */
        $product = self::getFixture('product');

        $multiData = array();
        foreach ($websitesNotAssignedToProduct as $websiteNotAssignedToProduct) {
            $notAssignedStoredDataId = array_shift($websiteNotAssignedToProduct->getStoreIds());
            $multiData[] = array(
                'website_id' => $websiteNotAssignedToProduct->getId(),
                'copy_to_stores' => array(
                    array(
                        'store_from' => $assignedStoredDataId,
                        'store_to' => $notAssignedStoredDataId,
                    )
                )
            );
        }
        $restResponse = $this->callPost('products/' . $product->getId() . '/websites', $multiData);
        $this->assertEquals(Mage_Webapi_Controller_Front_Rest::HTTP_MULTI_STATUS, $restResponse->getStatus());

        // Check response body
        $responseData = $restResponse->getBody();
        $this->assertArrayHasKey('messages', $responseData);
        $messages = $responseData['messages'];
        $this->assertArrayNotHasKey('error', $messages);
        $this->assertArrayHasKey('success', $messages);
        $this->assertEquals(count($websitesNotAssignedToProduct), count($messages['success']));
        $this->assertEquals($messages['success'][0]['message'], 'Resource updated successful.');
        $this->assertEquals($messages['success'][0]['code'], Mage_Webapi_Controller_Front_Rest::HTTP_OK);
        $this->assertEquals($messages['success'][0]['product_id'], $product->getId());
        $this->assertEquals($messages['success'][0]['website_id'], $websitesNotAssignedToProduct[0]->getId());


        // Check updated data
        /* @var $product Mage_Catalog_Model_Product */
        $product = Mage::getModel('Mage_Catalog_Model_Product')->load($product->getId());

        $existStoreData = Mage::getModel('Mage_Catalog_Model_Product')
            ->setStoreId($assignedStoredDataId)
            ->load($product->getId())
            ->toArray();
        unset($existStoreData['store_id']);
        unset($existStoreData['stock_item']['store_id']);

        foreach ($websitesNotAssignedToProduct as $websiteNotAssignedToProduct) {
            // Check website
            $this->assertContains($websiteNotAssignedToProduct->getId(), $product->getWebsiteIds());

            // Check stores
            $notAssignedStoredDataId = array_shift($websiteNotAssignedToProduct->getStoreIds());
            $newStoreData = Mage::getModel('Mage_Catalog_Model_Product')
                ->setStoreId($notAssignedStoredDataId)
                ->load($product->getId())
                ->toArray();
            unset($newStoreData['store_id']);
            unset($newStoreData['stock_item']['store_id']);

            $this->assertEquals($existStoreData, $newStoreData);
        }
    }

    /**
     * Test of the error representation of a website assignment to a product (POST method)
     *
     * @magentoDataFixture testsuite/Webapi/Catalog/Product/Website/_fixture/websites.php
     * @resourceOperation product_website::multicreate
     */
    public function testErrorRepresentationOnMultiAssignWebsiteToProduct()
    {
        /* @var $websiteNotAssignedToProduct Mage_Core_Model_Website */
        $websiteNotAssignedToProduct = array_shift(self::getFixture('websitesNotAssignedToProduct'));
        /* @var $websiteAssignedToProduct Mage_Core_Model_Website */
        $websiteAssignedToProduct = array_shift(self::getFixture('websitesAssignedToProduct'));
        /* @var $product Mage_Catalog_Model_Product */
        $product = self::getFixture('product');

        $multiData[] = array(
            'website_id' => $websiteNotAssignedToProduct->getId(),
            'copy_to_stores' => array(
                array(
                    'store_from' => 'invalid_id',
                    'store_to' => 'invalid_id',
                )
            )
        );
        $restResponse = $this->callPost('products/' . $product->getId() . '/websites', $multiData);
        $this->assertEquals(Mage_Webapi_Controller_Front_Rest::HTTP_MULTI_STATUS, $restResponse->getStatus());

        $responseData = $restResponse->getBody();
        $this->assertArrayHasKey('messages', $responseData);
        $messages = $responseData['messages'];
        $this->assertArrayHasKey('error', $messages);
        $this->assertEquals($messages['error'][0]['message'],
            sprintf(
                'Invalid value for "store_from" for the website with ID #%d.',
                $websiteNotAssignedToProduct->getId()
            )
        );
        $this->assertEquals($messages['error'][0]['code'], Mage_Webapi_Controller_Front_Rest::HTTP_BAD_REQUEST);
        $this->assertEquals($messages['error'][0]['product_id'], $product->getId());
        $this->assertEquals($messages['error'][0]['website_id'], $websiteNotAssignedToProduct->getId());
    }

    /**
     * Test of a website unassignment from a product (DELETE method)
     *
     * @magentoDataFixture testsuite/Webapi/Catalog/Product/Website/_fixture/websites.php
     * @resourceOperation product_website::delete
     */
    public function testWebsiteUnassignmentFromProduct()
    {
        /* @var $websiteAssignedToProduct Mage_Core_Model_Website */
        $websiteAssignedToProduct = array_shift(self::getFixture('websitesAssignedToProduct'));
        /* @var $product Mage_Catalog_Model_Product */
        $product = self::getFixture('product');

        $restResponse = $this->callDelete('products/' . $product->getId() . '/websites/'
            . $websiteAssignedToProduct->getId());
        $this->assertEquals(Mage_Webapi_Controller_Front_Rest::HTTP_OK, $restResponse->getStatus());

        /* @var $product Mage_Catalog_Model_Product */
        $product = Mage::getModel('Mage_Catalog_Model_Product')->load($product->getId());
        $this->assertNotContains($websiteAssignedToProduct->getId(), $product->getWebsiteIds());
    }

    /**
     * Test of a unavailable website unassignment from a product (DELETE method)
     *
     * @magentoDataFixture testsuite/Webapi/Catalog/Product/Website/_fixture/websites.php
     * @resourceOperation product_website::delete
     */
    public function testUnavailableWebsiteUnassignmentFromProduct()
    {
        /* @var $product Mage_Catalog_Model_Product */
        $product = self::getFixture('product');

        $restResponse = $this->callDelete('products/' . $product->getId() . '/websites/invalid_website_id');
        $this->assertEquals(Mage_Webapi_Controller_Front_Rest::HTTP_NOT_FOUND, $restResponse->getStatus());

        $responseData = $restResponse->getBody();
        $this->assertArrayHasKey('error', $responseData['messages']);
        $this->assertEquals($responseData['messages']['error'][0]['message'], 'Website #invalid_website_id not found.');
    }

    /**
     * Test of a website unassignment from an unavailable product (DELETE method)
     *
     * @magentoDataFixture testsuite/Webapi/Catalog/Product/Website/_fixture/websites.php
     * @resourceOperation product_website::delete
     */
    public function testWebsiteUnassignmentFromUnavailableProduct()
    {
        /* @var $websiteAssignedToProduct Mage_Core_Model_Website */
        $websiteAssignedToProduct = array_shift(self::getFixture('websitesAssignedToProduct'));
        /* @var $product Mage_Catalog_Model_Product */
        $product = self::getFixture('product');

        $restResponse = $this->callDelete('products/product_invalid_id/websites/' . $websiteAssignedToProduct->getId());
        $this->assertEquals(Mage_Webapi_Controller_Front_Rest::HTTP_NOT_FOUND, $restResponse->getStatus());

        $responseData = $restResponse->getBody();
        $this->assertArrayHasKey('error', $responseData['messages']);
        $this->assertEquals($responseData['messages']['error'][0]['message'], 'Product #product_invalid_id not found.');
    }

    /**
     * Test of an attempt to unassign a website that is not assigned to a product (DELETE method)
     *
     * @magentoDataFixture testsuite/Webapi/Catalog/Product/Website/_fixture/websites.php
     * @resourceOperation product_website::delete
     */
    public function testUnassignWebsiteThatIsNotAssignedToProduct()
    {
        /* @var $websiteNotAssignedToProduct Mage_Core_Model_Website */
        $websiteNotAssignedToProduct = array_shift(self::getFixture('websitesNotAssignedToProduct'));
        /* @var $product Mage_Catalog_Model_Product */
        $product = self::getFixture('product');

        $restResponse = $this->callDelete('products/' . $product->getId() . '/websites/'
            . $websiteNotAssignedToProduct->getId());
        $this->assertEquals(Mage_Webapi_Controller_Front_Rest::HTTP_BAD_REQUEST, $restResponse->getStatus());

        $responseData = $restResponse->getBody();
        $this->assertArrayHasKey('error', $responseData['messages']);
        $this->assertEquals(
            $responseData['messages']['error'][0]['message'],
            sprintf(
                'Product #%d isn\'t assigned to website #%d',
                $product->getId(),
                $websiteNotAssignedToProduct->getId()
            )
        );
    }

    /**
     * Test of retrieve an assigned website (GET method)
     *
     * @magentoDataFixture testsuite/Webapi/Catalog/Product/Website/_fixture/websites.php
     * @resourceOperation product_website::get
     */
    public function testRetrieveSingleAssignedWebsite()
    {
        /* @var $websiteAssignedToProduct Mage_Core_Model_Website */
        $websiteAssignedToProduct = array_shift(self::getFixture('websitesAssignedToProduct'));
        /* @var $product Mage_Catalog_Model_Product */
        $product = self::getFixture('product');

        $restResponse = $this->callGet('products/' . $product->getId() . '/websites/'
            . $websiteAssignedToProduct->getId());
        $this->assertEquals(Mage_Webapi_Controller_Front_Rest::HTTP_METHOD_NOT_ALLOWED, $restResponse->getStatus());
    }

    /**
     * Test of update an assigned website (PUT method)
     *
     * @magentoDataFixture testsuite/Webapi/Catalog/Product/Website/_fixture/websites.php
     * @resourceOperation product_website::update
     */
    public function testUpdateAssignedWebsite()
    {
        /* @var $websiteAssignedToProduct Mage_Core_Model_Website */
        $websiteAssignedToProduct = array_shift(self::getFixture('websitesAssignedToProduct'));
        /* @var $product Mage_Catalog_Model_Product */
        $product = self::getFixture('product');

        $restResponse = $this->callPut('products/' . $product->getId() . '/websites/'
            . $websiteAssignedToProduct->getId(), array('somedata'));
        $this->assertEquals(Mage_Webapi_Controller_Front_Rest::HTTP_METHOD_NOT_ALLOWED, $restResponse->getStatus());
    }
}
