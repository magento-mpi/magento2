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
 * @package     Magento_Test
 * @subpackage  integration_tests
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magento.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Test for stock items in API2
 *
 * @category    Magento
 * @package     Magento_Test
 * @author      Magento Api Team <api-team@magento.com>
 */
class Api2_CatalogInventory_Stock_Item_AdminTest extends Magento_Test_Webservice_Rest_Admin
{
    /**
     * Remove fixtures
     *
     * @return void
     */
    protected function tearDown()
    {
        $this->deleteFixture('product', true);
        $this->deleteFixture('stockItem', true);

        parent::tearDown();
    }

    /**
     * Delete acl fixture after test case
     */
    public static function tearDownAfterClass()
    {
        Magento_TestCase::deleteFixture('role', true);
        Magento_TestCase::deleteFixture('rule', true);
        Magento_TestCase::deleteFixture('attribute', true);
        Magento_Test_Webservice::setFixture('admin_acl_is_prepared', false);

        parent::tearDownAfterClass();
    }

    /**
     * Test retrieving existing product stock state
     *
     * @magentoDataFixture Api2/CatalogInventory/_fixtures/admin_acl.php
     * @magentoDataFixture Api2/CatalogInventory/_fixtures/product.php
     */
    public function testGet()
    {
        /* @var $stockItem Mage_CatalogInventory_Model_Stock_Item */
        $stockItem = $this->getFixture('stockItem');
        $restResponse = $this->callGet('stockitems/' . $stockItem->getId());
        
        Mage::dump('stockitems/' . $stockItem->getId(), $restResponse); exit;
        
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());

        $responseData = $restResponse->getBody();
        $this->assertNotEmpty($responseData);

        $stockItemOriginalData = $stockItem->getData();
        foreach ($stockItemOriginalData as $field => $value) {
            if (is_array($value)) {
                $this->assertEquals(count($stockItemOriginalData[$field]), count($value));
            } else {
                $this->assertEquals($stockItemOriginalData[$field], $value);
            }
        }
    }

    /**
     * Test retrieving not existing product stock state
     *
     * @magentoDataFixture Api2/CatalogInventory/_fixtures/admin_acl.php
     */
    public function testGetUnavailableResource()
    {
        $restResponse = $this->callGet('stockitems/' . 'invalid_id');
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_NOT_FOUND, $restResponse->getStatus());
    }

    /**
     * Test successful stock item update
     *
     * @magentoDataFixture Api2/CatalogInventory/_fixtures/admin_acl.php
     * @magentoDataFixture Api2/CatalogInventory/_fixtures/product.php
     */
    public function testUpdate()
    {
        $dataForUpdate  = require dirname(__FILE__) . '/../../_fixtures/stock_item_data.php';

        /* @var $stockItem Mage_CatalogInventory_Model_Stock_Item */
        $stockItem = $this->getFixture('stockItem');
        $restResponse = $this->callPut('stockitems/' . $stockItem->getId(), $dataForUpdate);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());

        /* @var $updatedStockItem Mage_CatalogInventory_Model_Stock_Item */
        $updatedStockItem = Mage::getModel('cataloginventory/stock_item')
            ->load($stockItem->getId());
        $updatedStockItemData = $updatedStockItem->getData();
        foreach ($dataForUpdate as $field => $value) {
            $this->assertEquals($value, $updatedStockItemData[$field]);
        }
    }

    /**
     * Test unsuccessful stock item update with empty required data
     *
     * @magentoDataFixture Api2/CatalogInventory/_fixtures/admin_acl.php
     * @magentoDataFixture Api2/CatalogInventory/_fixtures/product.php
     */
    public function testUpdateEmptyRequired()
    {
        $dataForUpdate  = require dirname(__FILE__) . '/../../_fixtures/stock_item_data_emptyrequired.php';

        /* @var $stockItem Mage_CatalogInventory_Model_Stock_Item */
        $stockItem = $this->getFixture('stockItem');
        $restResponse = $this->callPut('stockitems/' . $stockItem->getId(), $dataForUpdate);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_BAD_REQUEST, $restResponse->getStatus());

        $responseData = $restResponse->getBody();
        $errors = $responseData['messages']['error'];
        $this->assertNotEmpty($errors);

        $expectedErrors = array('Resource data pre-validation error.');
        foreach ($dataForUpdate as $key => $value) {
            $expectedErrors[] = sprintf('Empty value for "%s" in request.', $key);
        }
        $this->assertEquals(count($expectedErrors), count($errors));
        foreach ($errors as $error) {
            $this->assertContains($error['message'], $expectedErrors);
        }
    }

    /**
     * Test updating not existing stock item
     *
     * @magentoDataFixture Api2/CatalogInventory/_fixtures/admin_acl.php
     */
    public function testUpdateUnavailableResource()
    {
        $restResponse = $this->callPut('stockitems/' . 'invalid_id', array());
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_NOT_FOUND, $restResponse->getStatus());
    }
}
