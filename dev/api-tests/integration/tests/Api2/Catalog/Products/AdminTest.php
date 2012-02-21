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

class Api2_Catalog_Products_AdminTest extends Magento_Test_Webservice_Rest_Admin
{
    /**
     * Delete fixtures
     */
    protected function tearDown()
    {
        $this->deleteFixture('product_simple', true);
        parent::tearDown();
    }

    /**
     * Test product resource post
     */
    public function testPostSimpleRequiredFieldsOnly()
    {
        $productData = require dirname(__FILE__) . '/../_fixtures/Backend/SimpleProductData.php';

        $restResponse = $this->callPost('products', $productData);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());

        $location = $restResponse->getHeader('Location');
        list($productId) = array_reverse(explode('/', $location));
        /** @var $product Mage_Catalog_Model_Product */
        $product = Mage::getModel('catalog/product')->load($productId);
        $this->assertNotNull($product->getId());
        $this->setFixture('product_simple', $product);
        $this->assertEquals($product->getTypeId(), $productData['type']);
        $this->assertEquals($product->getAttributeSetId(), $productData['set']);

        $productAttributes = array_diff_key($productData, array_flip(array('type', 'set')));
        foreach ($productAttributes as $attribute => $value) {
            $this->assertEquals($product->getData($attribute), $value);
        }
    }

    /**
     * Test product resource post with all fields
     */
    public function testPostSimpleAllFieldsValid()
    {
        $productData = require dirname(__FILE__) . '/../_fixtures/Backend/SimpleProductAllFieldsData.php';

        $this->getWebService()->getClient()->setHeaders('Cookie', 'XDEBUG_SESSION=PHPSTORM');
        $restResponse = $this->callPost('products', $productData);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());

        $location = $restResponse->getHeader('Location');
        list($productId) = array_reverse(explode('/', $location));
        /** @var $product Mage_Catalog_Model_Product */
        $product = Mage::getModel('catalog/product')->load($productId);
        $this->assertNotNull($product->getId());
        $this->setFixture('product_simple', $product);
        $this->assertEquals($product->getTypeId(), $productData['type']);
        $this->assertEquals($product->getAttributeSetId(), $productData['set']);

        $dateAttributes = array('news_from_date', 'news_to_date', 'special_from_date', 'special_to_date',
            'custom_design_from', 'custom_design_to');
        foreach ($dateAttributes as $attribute) {
            $this->assertEquals(strtotime($productData[$attribute]), strtotime($product->getData($attribute)));
        }

        $exclude = array_merge($dateAttributes, array('type', 'set', 'group_price', 'tier_price', 'stock_data'));
        $productAttributes = array_diff_key($productData, array_flip($exclude));
        foreach ($productAttributes as $attribute => $value) {
            $this->assertEquals($product->getData($attribute), $value);
        }

        $stockItem = $product->getStockItem();
        foreach ($productData['stock_data'] as $attribute => $value) {
            $this->assertEquals($stockItem->getData($attribute), $value);
        }
    }

    /**
     * Test product resource post with all invalid fields
     * Negative test.
     */
    public function testPostSimpleAllFieldsInvalid()
    {
        $productData = require dirname(__FILE__) . '/../_fixtures/Backend/SimpleProductAllFieldsInvalidData.php';

        $this->getWebService()->getClient()->setHeaders('Cookie', 'XDEBUG_SESSION=PHPSTORM');
        $restResponse = $this->callPost('products', $productData);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_BAD_REQUEST, $restResponse->getStatus());

        $body = $restResponse->getBody();
        $errors = $body['messages']['error'];
        $this->assertNotEmpty($errors);

        $expectedErrors = array(
            'The SKU length should be 64 characters maximum.',
            'Resource data pre-validation error.',
        );
        $invalidValueAttributes = array('status', 'visibility', 'msrp_enabled', 'msrp_display_actual_price_type',
            'enable_googlecheckout', 'tax_class_id', 'custom_design', 'page_layout', 'options_container');
        foreach ($invalidValueAttributes as $attribute) {
            $expectedErrors[] = sprintf('Invalid value for attribute "%s".', $attribute);
        }
        $dateAttributes = array('news_from_date', 'news_to_date', 'special_from_date', 'special_to_date',
            'custom_design_from', 'custom_design_to');
        foreach ($dateAttributes as $attribute) {
            $expectedErrors[] = sprintf('Invalid date in the "%s" field.', $attribute);
        }
        $positiveNumberAttributes = array('weight', 'price', 'special_price', 'msrp');
        foreach ($positiveNumberAttributes as $attribute) {
            $expectedErrors[] = sprintf('Please enter a number 0 or greater in the "%s" field.', $attribute);
        }

        $this->assertEquals(count($expectedErrors), count($errors));
        foreach ($errors as $error) {
            $this->assertContains($error['message'], $expectedErrors);
        }

        // @TODO: implement tier price & group price validation & tests
    }

    /**
     * Test product create resource with empty required fields
     * Negative test.
     */
    public function testPostEmptyRequiredFields()
    {
        $this->getWebService()->getClient()->setHeaders('Cookie', 'XDEBUG_SESSION=PHPSTORM');
        $productData = require dirname(__FILE__) . '/../_fixtures/Backend/SimpleProductEmptyRequired.php';

        $restResponse = $this->callPost('products', $productData);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_BAD_REQUEST, $restResponse->getStatus());
        $body = $restResponse->getBody();
        $errors = $body['messages']['error'];
        $this->assertNotEmpty($errors);
        unset($productData['type']);
        unset($productData['set']);
        unset($productData['stock_data']);
        $expectedErrors = array(
            'Resource data pre-validation error.',
            'Empty value for "stock_data:qty" in request.'
        );
        foreach ($productData as $key => $value) {
            $expectedErrors[] = sprintf('Empty value for "%s" in request.', $key);
        }
        $this->assertEquals(count($expectedErrors), count($errors));
        foreach ($errors as $error) {
            $this->assertContains($error['message'], $expectedErrors);
        }
    }
}

