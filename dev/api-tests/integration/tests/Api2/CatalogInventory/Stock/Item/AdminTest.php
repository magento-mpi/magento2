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
     * Test retrieving existing product stock state
     *
     * @magentoDataFixture Api2/CatalogInventory/_fixtures/product.php
     * @magentoDataFixture Api2/CatalogInventory/_fixtures/admin_acl.php
     */
    public function testGet()
    {
        /* @var $stockItem Mage_Review_Model_Review */
        $stockItem = $this->getFixture('stockItem');
        $restResponse = $this->callGet('stockitems/' . $stockItem->getId());

        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());
        $responseData = $restResponse->getBody();

        $this->assertNotEmpty($responseData);
        $reviewOriginalData = $stockItem->getData();
        foreach ($reviewOriginalData as $field => $value) {
            if (is_array($value)) {
                $this->assertEquals(count($reviewOriginalData[$field]), count($value));
            } else {
                $this->assertEquals($reviewOriginalData[$field], $value);
            }
        }
    }

    /**
     * Test retrieving not existing product stock state
     */
    public function testGetUnavailableResource()
    {
        $restResponse = $this->callGet('stockitems/' . 'invalid_id');
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_NOT_FOUND, $restResponse->getStatus());
    }
}

