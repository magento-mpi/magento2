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
        $fixtureProducts = $this->getFixture('cataloginventory_stock_products');
        if ($fixtureProducts && count($fixtureProducts)) {
            foreach ($fixtureProducts as $fixtureProduct) {
                $this->callModelDelete($fixtureProduct, true);
            }
        }
        $this->deleteFixture('product', true);
        $this->deleteFixture('stockItem', true);

        parent::tearDown();
    }

    /**
     * Test get stock item
     *
     * @magentoDataFixture Api2/CatalogInventory/_fixtures/product.php
     */
    public function testGetStockItem()
    {
        /* @var $stockItem Mage_CatalogInventory_Model_Stock_Item */
        $stockItem = $this->getFixture('stockItem');

        $restResponse = $this->callGet('stockitems/' . $stockItem->getId());
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());

        $responseData = $restResponse->getBody();
        $this->assertNotEmpty($responseData);

        foreach ($responseData as $field => $value) {
            $this->assertEquals($stockItem->getData($field), $value);
        }
    }

    /**
     * Test get unavailable stock item
     */
    public function testGetUnavailableStockItemResource()
    {
        $restResponse = $this->callGet('stockitems/invalid_id');
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_NOT_FOUND, $restResponse->getStatus());
    }

    /**
     * Test get stock items
     *
     * @magentoDataFixture Api2/CatalogInventory/_fixtures/stock_items_list.php
     */
    public function testGetStockItems()
    {
        $restResponse = $this->callGet('stockitems', array('order' => 'item_id', 'dir' => Zend_Db_Select::SQL_DESC));
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());

        $responseData = $restResponse->getBody();
        $this->assertNotEmpty($responseData);

        $itemsIds = array();
        foreach ($responseData as $item) {
            $itemsIds[] = $item['item_id'];
        }
        $fixtureItems = $this->getFixture('cataloginventory_stock_items');
        foreach ($fixtureItems as $fixtureItem) {
            $this->assertContains($fixtureItem->getId(), $itemsIds,
                'Stock item should be in response');
        }
    }

    /**
     * Test successful stock item update
     *
     * @magentoDataFixture Api2/CatalogInventory/_fixtures/product.php
     */
    public function testUpdateStockItem()
    {
        /* @var $stockItem Mage_CatalogInventory_Model_Stock_Item */
        $stockItem = $this->getFixture('stockItem');

        $dataForUpdate  = require dirname(__FILE__) . '/../../_fixtures/stock_item_data.php';

        $restResponse = $this->callPut('stockitems/' . $stockItem->getId(), $dataForUpdate);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());

        /* @var $updatedStockItem Mage_CatalogInventory_Model_Stock_Item */
        $updatedStockItem = Mage::getModel('cataloginventory/stock_item')
            ->load($stockItem->getId());
        foreach ($dataForUpdate as $field => $value) {
            $this->assertEquals($value, $updatedStockItem->getData($field));
        }
    }

    /**
     * Test updating not existing stock item
     */
    public function testUpdateUnavailableStockItem()
    {
        $restResponse = $this->callPut('stockitems/invalid_id', array('min_qty' => 0));
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_NOT_FOUND, $restResponse->getStatus());
    }

    /**
     * Test successful stock items update with invalid data
     *
     * @magentoDataFixture Api2/CatalogInventory/_fixtures/product.php
     */
    public function testUpdateStockItemWithInvalidData()
    {
        /* @var $stockItem Mage_CatalogInventory_Model_Stock_Item */
        $stockItem = $this->getFixture('stockItem');
        $dataForUpdate  = require dirname(__FILE__) . '/../../_fixtures/stock_item_invalid_data.php';

        $restResponse = $this->callPut('stockitems/' . $stockItem->getId(), $dataForUpdate);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_BAD_REQUEST, $restResponse->getStatus());

        $responseData = $restResponse->getBody();
        $errors = $responseData['messages']['error'];
        $countOfErrorsInDataForUpdate = count($dataForUpdate) - 1;
        // Several errors can be returned because of one invalid position
        $this->assertGreaterThanOrEqual($countOfErrorsInDataForUpdate, count($errors));
    }

    /**
     * Test successful stock items update
     *
     * @magentoDataFixture Api2/CatalogInventory/_fixtures/stock_items_list.php
     */
    public function testUpdateStockItems()
    {
        $dataForUpdate = array();
        $fixtureItems = $this->getFixture('cataloginventory_stock_items');
        foreach ($fixtureItems as $fixtureItem) {
            $singleItemDataForUpdate = require dirname(__FILE__) . '/../../_fixtures/stock_item_data.php';
            $singleItemDataForUpdate['item_id'] = $fixtureItem->getId();
            $dataForUpdate[] = $singleItemDataForUpdate;
        }

        $restResponse = $this->callPut('stockitems', $dataForUpdate);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_MULTI_STATUS, $restResponse->getStatus());

        $responseData = $restResponse->getBody();
        $successes = $responseData['success'];
        $this->assertEquals(count($successes), count($fixtureItems));

        foreach ($fixtureItems as $key => $fixtureItem) {
            /* @var $updatedStockItem Mage_CatalogInventory_Model_Stock_Item */
            $updatedStockItem = Mage::getModel('cataloginventory/stock_item')
                ->load($fixtureItem->getId());
            $updatedStockItemData = $updatedStockItem->getData();
            $dataForUpdateSingleItem = $dataForUpdate[$key];
            foreach ($dataForUpdateSingleItem as $field => $value) {
                $this->assertEquals($value, $updatedStockItemData[$field]);
            }
        }
    }

    /**
     * Test unsuccessful stock items update with empty required data
     */
    public function testUpdateUnavailableStockItems()
    {
        $invalidId = -1;
        $singleItemDataForUpdate = array('item_id' => $invalidId);
        $dataForUpdate = array($singleItemDataForUpdate);

        $restResponse = $this->callPut('stockitems', $dataForUpdate);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_MULTI_STATUS, $restResponse->getStatus());

        $responseData = $restResponse->getBody();
        $errors = $responseData['error'];
        $this->assertEquals(count($errors), 1);

        $expectedError = array(
            'message' => sprintf('StockItem #%d not found.', $invalidId),
            'code'    => Mage_Api2_Model_Server::HTTP_BAD_REQUEST,
            'item_id' => $invalidId
        );
        $this->assertEquals($errors[0], $expectedError);
    }

    /**
     * Test unsuccessful stock items update with missing ItemId
     */
    public function testUpdateStockItemsWithMissingItemId()
    {
        $singleItemDataForUpdate = require dirname(__FILE__) . '/../../_fixtures/stock_item_data.php';
        unset($singleItemDataForUpdate['item_id']); // missing item_id
        $dataForUpdate = array($singleItemDataForUpdate);

        $restResponse = $this->callPut('stockitems', $dataForUpdate);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_MULTI_STATUS, $restResponse->getStatus());

        $responseData = $restResponse->getBody();
        $this->assertEquals(count($responseData['error']), 1);

        $expectedError = array(
            'item_id' => '',
            'message' => 'Invalid value for "item_id" in request.',
            'code'    => Mage_Api2_Model_Server::HTTP_BAD_REQUEST,
        );
        $this->assertEquals($responseData['error'][0], $expectedError);
    }

    /**
     * Test unsuccessful stock items update with missing ItemId
     */
    public function testUpdateStockItemsWithInvalidItemId()
    {
        $singleItemDataForUpdate = require dirname(__FILE__) . '/../../_fixtures/stock_item_data.php';
        $singleItemDataForUpdate['item_id'] = 'invalid_id'; // wrong item_id
        $dataForUpdate = array($singleItemDataForUpdate);

        $restResponse = $this->callPut('stockitems', $dataForUpdate);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_MULTI_STATUS, $restResponse->getStatus());

        $responseData = $restResponse->getBody();
        $errors = $responseData['error'];
        $this->assertEquals(count($errors), 1);

        $expectedError = array(
            'item_id' => 'invalid_id',
            'message' => 'Invalid value for "item_id" in request.',
            'code'    => Mage_Api2_Model_Server::HTTP_BAD_REQUEST,
        );
        $this->assertEquals($errors[0], $expectedError);
    }

    /**
     * Test successful stock items update with invalid data
     *
     * @magentoDataFixture Api2/CatalogInventory/_fixtures/stock_items_list.php
     */
    public function testUpdateStockItemsWithInvalidData()
    {
        $dataForUpdate = array();
        $fixtureItems = $this->getFixture('cataloginventory_stock_items');
        $singleItemDataForUpdate = require dirname(__FILE__) . '/../../_fixtures/stock_item_invalid_data.php';
        foreach ($fixtureItems as $fixtureItem) {
            $singleItemDataForUpdate['item_id'] = $fixtureItem->getId();
            $dataForUpdate[] = $singleItemDataForUpdate;
        }

        $restResponse = $this->callPut('stockitems', $dataForUpdate);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_MULTI_STATUS, $restResponse->getStatus());

        $responseData = $restResponse->getBody();
        $errors = $responseData['error'];
        $countOfErrorsInSingleItemDataForUpdate = count($singleItemDataForUpdate) - 1;
        // Several errors can be returned because of one invalid position
        $this->assertGreaterThanOrEqual(count($fixtureItems) * $countOfErrorsInSingleItemDataForUpdate, count($errors));
    }
}
