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
 * Helper class
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class CompareProducts_Helper extends Mage_Selenium_TestCase
{


    /**
     * Removes all products from the Compare Products
     *
     * Preconditions: page with Compare Products block is opened
     *
    */
    public function clearAll()
    {
       //Check for  Compare Products block
       //click('ClearAll');
    }

    /**
     * Removes product from the Compare Products block
     *
     * Preconditions: page with Compare Products block is opened
     *
     * @param string $productName Name of product to be deleted
    */
    public function removeProductFromCpBlock($productName)
    {
        //Check for Compare Products block
        //Check for product
        //remove product
    }

     /**
     * Removes product from the Compare Products pop-up
     *
     * Preconditions: Compare Products pop-up is opened
     *
     * @param string $productName Name of product to be deleted
    */
    public function removeProductFromCpPopup($productName)
    {
        //Check for Compare Products pop-up
        //Check for product
        //remove product
    }

    /**
     * Get available product details from the Compare Products pop-up
     *
     * Preconditions: Compare Products pop-up is opened
     *
     * @param string $productName Name of product to be grabbed
     * @return array|null $productData Product details from Compare Products pop-up
    */
    public function getProductDetailsFromCpPopup($productName)
    {
        //Check for product
        //getAttributeslist
        //grab product details
    }


     /**
     * Get list of available product attributes in Compare Products pop-up
     *
     * Preconditions: Compare Products pop-up is opened
     *
     * @return array $attributesList Array of available product attributes in Compare Products pop-up
     *
    */
    public function getAttributesList()
    {
        //grab available product attributes
    }

    /**
     * Get list of available products in Compare Products pop-up
     *
     * Preconditions: Compare Products pop-up is opened
     *
    */
    public function getProductsList()
    {
          //grab available product names
    }



     /**
     * Compare provided products data with actual info in Compare Products pop-up
     *
     * Preconditions: Compare Products pop-up is opened
     *
     * @param array $productsData Array of products info to be checked
    */
    public function verifyProducts($productsData)
    {
        // foreach getProductsList()
        //     dispalyedProducts[] = getProductDetailsFromCpPopup()
        //
        //Compare $productsData&dispalyedProducts
    }

}
