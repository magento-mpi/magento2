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
 * @category    tests
 * @package     selenium
 * @subpackage  tests
 * @author      Magento Core Team <core@magentocommerce.com>
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Helper class
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Enterprise2_Mage_AddBySku_Helper extends Mage_Selenium_TestCase
{
     /**
     * Create Simple Product for tests
     *
     * @return array
     * @test
     */
    public function createSimpleProduct($product)
    {
        //Data
        $productDataSimple = $this->loadDataSet('AddBySku', $product);
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productDataSimple);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        return array ('sku' => $productDataSimple['general_sku']);
    }
    
    public function createVirtualProduct ($product)
    {
         //Data
        $productDataVirtual = $this->loadDataSet('Product', $product);
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productDataVirtual, 'virtual');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        return array ('sku' => $productDataVirtual['general_sku']);
    }
    /*Verify adding additional rows for adding products to order by SKU */
    public function verifySkuQtyRow ($row)
    {
        $this->addParameter('row', $row);
        $this->assertTrue($this->controlIsPresent('field','sku'),
                                                  'There is no SKU field on the page');
        $this->assertTrue($this->controlIsPresent('field','qty_sku'),
                                                  'There is no Qty field on the page');
        return ($row);
    }
    /*Verify added products in Items Ordered grid */
    public function verifyProductAdded ($sku)
    {
        $this->addParameter('skuproduct', $sku);
        $this->assertTrue($this->ControlIsPresent('pageelement','sku_product_added'),
                                                   "There is no product with SKU: $sku in Items Ordered grid");
        return ($sku);
    }
    /*Delete created product */
    public function postConditionDeleteProduct($createdproduct)
    {
        //Data
        $productData = $this->loadDataSet('AddBySku', "$createdproduct");
        $search = $this->loadDataSet('Product', 'product_search', array('product_sku' => $productData['general_sku']));
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->openProduct($search);
        $this->clickButtonAndConfirm('delete', 'confirmation_for_delete');
        //Verifying
        $this->assertMessagePresent('success', 'success_deleted_product');
    }
    /*Verify presence of product in required attention grid  */
    public function verifyProductRequiredAttention ($sku)
    {
        $this->addParameter('skuproduct', $sku);
        $this->assertTrue($this->ControlIsPresent('pageelement','sku_product_required'),
                                                  "There is no SKU: $sku in 'Product(s) require attention' grid");
        return ($sku);
    }
    
    /*Verify absence of product in required attention grid and Items Ordered grid  */
    public function verifyProductAbsentOnPage ($sku)
    {
        $this->addParameter('skuproduct', $sku);
        $this->assertFalse($this->ControlIsPresent('pageelement','sku_product_required'),
                                                   "Product with SKU: $sku is present in required grid the page");
        $this->assertFalse($this->ControlIsPresent('pageelement','sku_product_added'),
                                                   "Product with SKU: $sku is present in items ordered");
        return ($sku);
    }
    
    /*Parameter to identify XPath of Delete button in Require Attention grid */
    public function identifySkuDeleteButton ($row)
    {
        $this->addParameter('row', $row);
        $this->assertTrue($this->controlIsPresent('button','remove_one_invalid'),
                                                  "There is no button to delete one product separately in row #$row");
        return ($row);
    }
}
