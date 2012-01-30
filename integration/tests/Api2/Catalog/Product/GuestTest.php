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
 * Test product resource
 *
 * @category    Magento
 * @package     Magento_Test
 * @author      Magento Api Team <api-team@magento.com>
 */

class Api2_Catalog_Product_GuestTest extends Magento_Test_Webservice_Rest_Guest
{
    /**
     * Tear down
     *
     * @return void
     */
    protected function tearDown()
    {
        $product = new Mage_Catalog_Model_Product();
        $product->load($this->getFixture('productId'));
        $this->callModelDelete($product, true);

        parent::tearDown();
    }

    /**
     * Test product resource get
     *
     * @magentoDataFixture Api/SalesOrder/_fixtures/product_simple.php
     * @return void
     */
    public function testGet()
    {
        /** @var $product Mage_Catalog_Model_Product */
        $product = $this->getFixture('product_simple');
        $this->setFixture('productId', $product->getId());
        $restResponse = $this->getWebService()->callGet('products/' . $product->getId());
        $this->assertEquals(200, $restResponse->getStatus());
        $body = $restResponse->getBody();
        $this->assertEquals($product->getName(), $body['name']);
        $this->assertEquals($product->getSku(), $body['sku']);
    }

    /**
     * Test product resource delete
     *
     * @magentoDataFixture Api/SalesOrder/_fixtures/product_simple.php
     * @return void
     */
    public function testDelete()
    {
        /** @var $product Mage_Catalog_Model_Product */
        $product = $this->getFixture('product_simple');
        $this->setFixture('productId', $product->getId());

        $restResponse = $this->getWebService()->callDelete('products/' . $product->getId());
        // @todo: fix test when resource method implemented
        $this->assertEquals(405, $restResponse->getStatus());
    }

    /**
     * Test product resource post
     *
     * @return void
     */
    public function testPost()
    {
        $productData = require dirname(__FILE__) . '/_fixtures/ProductData.php';

        $restResponse = $this->getWebService()->callPost('products', $productData['guest']['full']);
        $this->assertEquals(200, $restResponse->getStatus());

        // @todo: implement test when product resource is fixed
    }

    /**
     * Test product resource put
     *
     * @magentoDataFixture Api/SalesOrder/_fixtures/product_simple.php
     * @return void
     */
    public function testPut()
    {
        /** @var $product Mage_Catalog_Model_Product */
        $product = $this->getFixture('product_simple');
        $this->setFixture('productId', $product->getId());

        $productData = require dirname(__FILE__) . '/_fixtures/ProductData.php';
        $restResponse = $this->getWebService()->callPut('products/' . $product->getId(),
            $productData['guest']['update']);
        $this->assertEquals(200, $restResponse->getStatus());

        // @todo: implement test when product resource is fixed
    }
}

