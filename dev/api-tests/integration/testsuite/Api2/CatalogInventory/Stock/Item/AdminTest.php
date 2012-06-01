<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Test
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
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
     * @magentoDataFixture Api2/CatalogInventory/_fixture/product.php
     * @resourceOperation stock_item::get
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
     *
     * @resourceOperation stock_item::get
     */
    public function testGetUnavailableStockItemResource()
    {
        $restResponse = $this->callGet('stockitems/invalid_id');
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_NOT_FOUND, $restResponse->getStatus());
    }

    /**
     * Test get stock items
     *
     * @magentoDataFixture CatalogInventory/Stock/Item/stock_items_list.php
     * @resourceOperation stock_item::multiget
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
     * @magentoDataFixture Api2/CatalogInventory/_fixture/product.php
     * @resourceOperation stock_item::update
     */
    public function testUpdateStockItem()
    {
        /* @var $stockItem Mage_CatalogInventory_Model_Stock_Item */
        $stockItem = $this->getFixture('stockItem');

        $dataForUpdate  = $this->_loadStockItemData();

        $restResponse = $this->callPut('stockitems/' . $stockItem->getId(), $dataForUpdate);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());

        /* @var $updatedStockItem Mage_CatalogInventory_Model_Stock_Item */
        $updatedStockItem = Mage::getModel('Mage_CatalogInventory_Model_Stock_Item')
            ->load($stockItem->getId());
        foreach ($dataForUpdate as $field => $value) {
            $this->assertEquals($value, $updatedStockItem->getData($field));
        }
    }

    /**
     * Test updating not existing stock item
     *
     * @resourceOperation stock_item::update
     */
    public function testUpdateUnavailableStockItem()
    {
        $restResponse = $this->callPut('stockitems/invalid_id', array('min_qty' => 0));
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_NOT_FOUND, $restResponse->getStatus());
    }

    /**
     * Test successful stock items update with invalid data
     *
     * @magentoDataFixture Api2/CatalogInventory/_fixture/product.php
     * @resourceOperation stock_item::update
     */
    public function testUpdateStockItemWithInvalidData()
    {
        /* @var $stockItem Mage_CatalogInventory_Model_Stock_Item */
        $stockItem = $this->getFixture('stockItem');
        $dataForUpdate  = require TEST_FIXTURE_DIR . '/_data/CatalogInventory/Stock/Item/stock_item_invalid_data.php';
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
     * @magentoDataFixture CatalogInventory/Stock/Item/stock_items_list.php
     * @resourceOperation stock_item::multiupdate
     */
    public function testUpdateStockItems()
    {
        $dataForUpdate = array();
        $fixtureItems = $this->getFixture('cataloginventory_stock_items');
        foreach ($fixtureItems as $fixtureItem) {
            $singleItemDataForUpdate = $this->_loadStockItemData();
            $singleItemDataForUpdate['item_id'] = $fixtureItem->getId();
            $dataForUpdate[] = $singleItemDataForUpdate;
        }

        $restResponse = $this->callPut('stockitems', $dataForUpdate);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_MULTI_STATUS, $restResponse->getStatus());

        $responseData = $restResponse->getBody();
        $this->assertArrayHasKey('messages', $responseData);
        $successes = $responseData['messages']['success'];
        $this->assertEquals(count($successes), count($fixtureItems));

        foreach ($fixtureItems as $key => $fixtureItem) {
            /* @var $updatedStockItem Mage_CatalogInventory_Model_Stock_Item */
            $updatedStockItem = Mage::getModel('Mage_CatalogInventory_Model_Stock_Item')
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
     *
     * @resourceOperation stock_item::multiupdate
     */
    public function testUpdateUnavailableStockItems()
    {
        $invalidId = -1;
        $singleItemDataForUpdate = array('item_id' => $invalidId);
        $dataForUpdate = array($singleItemDataForUpdate);

        $restResponse = $this->callPut('stockitems', $dataForUpdate);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_MULTI_STATUS, $restResponse->getStatus());

        $responseData = $restResponse->getBody();
        $this->assertArrayHasKey('messages', $responseData);
        $errors = $responseData['messages']['error'];
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
     *
     * @resourceOperation stock_item::multiupdate
     */
    public function testUpdateStockItemsWithMissingItemId()
    {
        $singleItemDataForUpdate = $this->_loadStockItemData();
        unset($singleItemDataForUpdate['item_id']); // missing item_id
        $dataForUpdate = array($singleItemDataForUpdate);

        $restResponse = $this->callPut('stockitems', $dataForUpdate);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_MULTI_STATUS, $restResponse->getStatus());

        $responseData = $restResponse->getBody();
        $this->assertArrayHasKey('messages', $responseData);
        $this->assertEquals(count($responseData['messages']['error']), 1);

        $expectedError = array(
            'item_id' => '',
            'message' => 'Invalid value for "item_id" in request.',
            'code'    => Mage_Api2_Model_Server::HTTP_BAD_REQUEST,
        );
        $this->assertEquals($responseData['messages']['error'][0], $expectedError);
    }

    /**
     * Test unsuccessful stock items update with missing ItemId
     *
     * @resourceOperation stock_item::multiupdate
     */
    public function testUpdateStockItemsWithInvalidItemId()
    {
        $singleItemDataForUpdate = $this->_loadStockItemData();
        $singleItemDataForUpdate['item_id'] = 'invalid_id'; // wrong item_id
        $dataForUpdate = array($singleItemDataForUpdate);

        $restResponse = $this->callPut('stockitems', $dataForUpdate);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_MULTI_STATUS, $restResponse->getStatus());

        $responseData = $restResponse->getBody();
        $this->assertArrayHasKey('messages', $responseData);
        $errors = $responseData['messages']['error'];
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
     * @magentoDataFixture CatalogInventory/Stock/Item/stock_items_list.php
     * @resourceOperation stock_item::multiupdate
     */
    public function testUpdateStockItemsWithInvalidData()
    {
        $dataForUpdate = array();
        $fixtureItems = $this->getFixture('cataloginventory_stock_items');
        $singleItemDataForUpdate  = require TEST_FIXTURE_DIR
            . '/_data/CatalogInventory/Stock/Item/stock_item_invalid_data.php';
        foreach ($fixtureItems as $fixtureItem) {
            $singleItemDataForUpdate['item_id'] = $fixtureItem->getId();
            $dataForUpdate[] = $singleItemDataForUpdate;
        }

        $restResponse = $this->callPut('stockitems', $dataForUpdate);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_MULTI_STATUS, $restResponse->getStatus());

        $responseData = $restResponse->getBody();
        $this->assertArrayHasKey('messages', $responseData);
        $errors = $responseData['messages']['error'];
        $countOfErrorsInSingleItemDataForUpdate = count($singleItemDataForUpdate) - 1;
        // Several errors can be returned because of one invalid position
        $this->assertGreaterThanOrEqual(count($fixtureItems) * $countOfErrorsInSingleItemDataForUpdate, count($errors));
    }

    /**
     * Load valid stock item data
     *
     * @return array
     */
    protected function _loadStockItemData()
    {
        return require TEST_FIXTURE_DIR . '/_data/CatalogInventory/Stock/Item/stock_item_data.php';
    }
}
