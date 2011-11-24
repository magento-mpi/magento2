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
 * Product Tax Class deletion tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Tax_ProductTaxClass_DeleteTest extends Mage_Selenium_TestCase
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
     * <p>Navigate to Sales-Tax-Product Tax Classes</p>
     */
    protected function assertPreConditions()
    {
        // @TODO
    }

    /**
     * <p>Delete a Product Tax Class</p>
     * <p>Steps:</p>
     * <p>1. Create a new Product Tax Class</p>
     * <p>2. Open the Product Tax Class</p>
     * <p>3. Delete the Product Tax Class</p>
     * <p>Expected result:</p>
     * <p>Received the message that the Product Tax Class has been deleted.</p>
     *
     * @test
     */
    public function notUsedInRule()
    {
        $this->markTestIncomplete('@TODO');
    }

    /**
     * <p>Delete a Product Tax Class that used in Tax Rule</p>
     * <p>Steps:</p>
     * <p>1. Create a new Product Tax Class</p>
     * <p>2. Create a new Tax Rule that use Product Tax Class from previous step</p>
     * <p>2. Open the Product Tax Class</p>
     * <p>3. Delete the Product Tax Class</p>
     * <p>Expected result:</p>
     * <p>Received the message that the Product Tax Class could not be deleted.</p>
     *
     * @test
     */
    public function usedInRule()
    {
        $this->markTestIncomplete('@TODO');
    }

        /**
     * <p>Delete a Product Tax Class that used in Product</p>
     * <p>Steps:</p>
     * <p>1. Create a new Product Tax Class</p>
     * <p>2. Create a new Product that use Product Tax Class from previous step</p>
     * <p>2. Open the Product Tax Class</p>
     * <p>3. Delete the Product Tax Class</p>
     * <p>Expected result:</p>
     * <p>Received the message that the Product Tax Class could not be deleted.</p>
     * <p>Error message: You cannot delete this tax class as it is used for 1 products.</p>
     *
     * @test
     */
    public function usedInProduct()
    {
        $this->markTestIncomplete('@TODO');
    }


}
