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
 * Test for stock items collection API2
 *
 * @category    Magento
 * @package     Magento_Test
 * @author      Magento Api Team <api-team@magento.com>
 */
class Api2_CatalogInventory_Stock_Items_AdminTest extends Magento_Test_Webservice_Rest_Admin
{
    /**
     * Prepare ACL
     */
    public static function setUpBeforeClass()
    {
        require dirname(__FILE__) . '/../../_fixtures/admin_acl.php';

        parent::setUpBeforeClass();
    }

    /**
     * Delete fixtures
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

        // stock items are deleted by foreign key

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
     * Test get stock items for admin
     *
     * @magentoDataFixture Api2/CatalogInventory/_fixtures/stock_items_list.php
     */
    public function testGet()
    {
        $restResponse = $this->callGet('stockitems', array('order' => 'item_id', 'dir' => Zend_Db_Select::SQL_DESC));
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());

        $items = $restResponse->getBody();
        $this->assertNotEmpty($items);
        $itemsIds = array();
        foreach ($items as $item) {
            $itemsIds[] = $item['item_id'];
        }

        $fixtureItems = $this->getFixture('cataloginventory_stock_items');
        foreach ($fixtureItems as $fixtureItem) {
            $this->assertContains($fixtureItem->getId(), $itemsIds,
                'Stock item should be in response');
        }
    }

    /**
     * Test successful stock items update
     *
     * @magentoDataFixture Api2/CatalogInventory/_fixtures/stock_items_list.php
     */
    public function testUpdate()
    {
        $dataForUpdate = array();
        $fixtureItems = $this->getFixture('cataloginventory_stock_items');
        foreach ($fixtureItems as $fixtureItem) {
            $singleItemDataForUpdate = require dirname(__FILE__) . '/../../_fixtures/stock_item_data.php';
            $singleItemDataForUpdate['item_id'] = $fixtureItem->getId();
            $dataForUpdate[] = $singleItemDataForUpdate;
        }

        $restResponse = $this->callPut('stockitems', $dataForUpdate);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());

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
     * Test unsuccessful stock item update with empty required data
     */
    public function testUpdateUnavailableResource()
    {
        $invalidId = 'invalid_id';
        $singleItemDataForUpdate = array('item_id' => $invalidId);
        $dataForUpdate = array($singleItemDataForUpdate);

        $restResponse = $this->callPut('stockitems', $dataForUpdate);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());

        $responseData = $restResponse->getBody();
        $errors = $responseData['error'];
        $this->assertEquals(count($errors), 1);

        $expectedError = array(
            'message' => Mage_Api2_Model_Resource::RESOURCE_NOT_FOUND,
            'code'    => Mage_Api2_Model_Server::HTTP_NOT_FOUND,
            'item_id' => $invalidId
        );
        $this->assertEquals($errors[0], $expectedError);
    }

    /**
     * Test unsuccessful stock item update with empty required data
     */
    public function testUpdateMissingItemId()
    {
        $singleItemDataForUpdate = require dirname(__FILE__) . '/../../_fixtures/stock_item_data.php';
        unset($singleItemDataForUpdate['item_id']);
        $dataForUpdate = array($singleItemDataForUpdate);

        $restResponse = $this->callPut('stockitems', $dataForUpdate);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());

        $responseData = $restResponse->getBody();
        $errors = $responseData['error'];
        $this->assertEquals(count($errors), 1);

        $expectedError = array(
            'message' => 'Missing "item_id" in request.',
            'code'    => Mage_Api2_Model_Server::HTTP_BAD_REQUEST,
        );
        $this->assertEquals($errors[0], $expectedError);
    }

    /**
     * Test unsuccessful stock item update with empty required data
     *
     * @magentoDataFixture Api2/CatalogInventory/_fixtures/product.php
     */
    public function testUpdateEmptyRequired()
    {
        /* @var $stockItem Mage_CatalogInventory_Model_Stock_Item */
        $stockItem = $this->getFixture('stockItem');
        $itemId = $stockItem->getId();

        $singleItemDataForUpdate['item_id'] = NULL; // for this case item_id is NULL also
        $singleItemDataForUpdate = array_merge(
            $singleItemDataForUpdate,
            require dirname(__FILE__) . '/../../_fixtures/stock_item_data_emptyrequired.php'
        );
        $dataForUpdate = array($singleItemDataForUpdate);

        $restResponse = $this->callPut('stockitems', $dataForUpdate);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());

        $responseData = $restResponse->getBody();
        $errors = $responseData['error'];
        $this->assertNotEmpty($errors);

        $expectedErrors = array();
        foreach ($singleItemDataForUpdate as $key => $value) {
            $expectedErrors[] = array(
                'message' => sprintf('Empty value for "%s" in request.', $key),
                'code'    => Mage_Api2_Model_Server::HTTP_BAD_REQUEST,
            );
        }
        $this->assertEquals(count($expectedErrors), count($errors));

        foreach ($errors as $key => $error) {
            $this->assertEquals($error, $expectedErrors[$key]);
        }
    }
}
