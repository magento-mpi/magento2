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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    WebService_CatalogInventory_StockItemTest
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

if (!defined('_IS_INCLUDED')) {
    require dirname(__FILE__) . '/../PHPUnitTestInit.php';
    PHPUnitTestInit::runMe(__FILE__);
}

class WebService_CatalogInventory_StockItemTest extends WebService_TestCase_Abstract
{
    /**
     * tests cataloginventory_stock_item.list
     *
     * @dataProvider connectorProvider
     */
    public function testList(WebService_Connector_Interface $connector)
    {
        $skus = array(uniqid(), uniqid());

        $attributeSets = $connector->call('product_attribute_set.list');
        $set = current($attributeSets);

        $products = array();
        foreach ($skus as $sku) {
            $productId = $connector->call('product.create', array('simple', $set['set_id'], $sku, array('name' => uniqid())));
            $products[$productId]['sku'] = $sku;
            $products[$productId]['qty'] = mt_rand(0,100);
            $products[$productId]['is_in_stock'] = mt_rand(0,1);

            $connector->call('product_stock.update', array($productId, $products[$productId]));
        }
        $list = $connector->call('product_stock.list', array(array_keys($products)));
        $expected = array();
        foreach ($products as $productId => $product) {
            $expected[] = array(
                'product_id'    => $productId,
                'sku'           => $product['sku'],
                'qty'           => $product['qty'],
                'is_in_stock'   => $product['is_in_stock']
            );

            $connector->call('product.delete', array($productId));
        }
        $this->assertEquals($expected, $list);
    }

    /**
     * tests cataloginventory_stock_item.update
     *
     * @dataProvider connectorProvider
     */
    public function testUpdate(WebService_Connector_Interface $connector)
    {
        $attributeSets = $connector->call('product_attribute_set.list');
        $set = current($attributeSets);
        $sku = uniqid();
        $productId = $connector->call('product.create', array('simple', $set['set_id'], $sku, array('name' => uniqid())));

        $expected = array(
            'product_id'    => $productId,
            'sku'           => $sku,
            'qty'           => mt_rand(0,100),
            'is_in_stock'   => mt_rand(0,1)
        );

        $connector->call('product_stock.update', array($productId, $expected));
        $result = $connector->call('product_stock.list', array(array($productId)));

        $connector->call('product.delete', array($productId));

        $this->assertEquals($expected, $result[0]);
    }
}