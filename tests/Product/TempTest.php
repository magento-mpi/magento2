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
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Simple product creation tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Product_TempTest extends Mage_Selenium_TestCase
{

    /**
     * <p>Log in to Backend.</p>
     */
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
    }

    /**
     * <p>Preconditions:</p>
     * <p>Navigate to Catalog -> Manage Products</p>
     */
    protected function assertPreConditions()
    {
        $this->navigate('manage_products');
//        $this->assertTrue($this->checkCurrentPage('manage_products'), $this->messages);
        $this->addParameter('id', '0');
    }


    /**
     * @test
     */
    public function allFieldsInSimple()
    {
        //Data
        $productData = $this->loadData('frontend_simple_product_details_validation',
                NULL, array('general_name', 'general_sku'));
        $productSearch = $this->loadData('product_search', array('product_sku' => $productData['general_sku']));
        //Steps
        $this->productHelper()->createProduct($productData);
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_product'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_products'), $this->messages);
        //Verifying
        $xpathArray = $this->productHelper()->frontVerifyProductInfo($productData);

    }

}