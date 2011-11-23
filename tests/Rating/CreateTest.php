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
 * Rating creation into backend
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Rating_CreateTest extends Mage_Selenium_TestCase
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
     * <p>Navigate to Catalog -> Reviews and Ratings -> Manage Ratings</p>
     */
    protected function assertPreConditions()
    {
        $this->navigate('manage_ratings');
        $this->addParameter('storeId', '1');
    }
    /**
     * <p>Creating Rating with required fields only</p>
     *
     * <p>Steps:</p>
     * <p>1. Click "Add New Rating" button;</p>
     * <p>2. Fill in required fields by regular data;</p>
     * <p>3. Click "Save Rating" button;</p>
     * <p>Expected result:</p>
     * <p>Success message appears - rating saved</p>
     *
     * @test
     */
    public function withRequiredFieldsOnly()
    {
        $this->markTestIncomplete('@TODO');
    }

    /**
     * <p>Creating Rating with empty required fields</p>
     *
     * <p>Steps:</p>
     * <p>1. Click "Add New Rating" button;</p>
     * <p>2. Leave required fields empty;</p>
     * <p>3. Click "Save Rating" button;</p>
     * <p>Expected result:</p>
     * <p>Error message appears - "This is a required field";</p>
     *
     * @test
     */
    public function withEmptyDefaultValue()
    {
        $this->markTestIncomplete('@TODO');
    }

    /**
     * <p>Creating Rating with Visible In option</p>
     *
     * <p>Preconditions:</p>
     * <p>Store View created</p>
     * <p>Product created</p>
     * <p>Steps:</p>
     * <p>1. Click "Add New Rating" button;</p>
     * <p>2. Fill in necessary fields by regular data - select created store view into "Visible In" block;</p>
     * <p>3. Click "Save Rating" button;</p>
     * <p>Expected result:</p>
     * <p>Success message appears - rating saved</p>
     *
     * <p>Verification:</p>
     * <p>Goto Frontend;</p>
     * <p>Open created Product</p>
     * <p>Verify that rating is absent on Product Page;</p>
     * <p>Switch to created Store View;</p>
     * <p>Navigate to Product Page</p>
     * <p>Verify that rating is present on product page</p>
     *
     * @test
     */
    public function withVisibleIn()
    {
        $this->markTestIncomplete('@TODO');
    }

    /**
     * <p>Creating a new rating with long values into required fields</p>
     * <p>Steps:</p>
     * <p>1. Click button "Add New Rating"</p>
     * <p>2. Fill in fields in Rating Details area by long values</p>
     * <p>4. Click button "Save Rating"</p>
     * <p>Expected result:</p>
     * <p>Received the message that the rating has been saved.</p>
     *
     * @test
     */
    public function withLongValues()
    {
        $this->markTestIncomplete('@TODO');
    }

    /**
     * <p>Creating a new rating with incorrect length into required fields</p>
     * <p>Steps:</p>
     * <p>1. Click button "Add New Rating"</p>
     * <p>2. Fill in fields in Rating Details area by long values</p>
     * <p>4. Click button "Save Rating"</p>
     * <p>Expected result:</p>
     * <p>Received the message that the rating has been saved.</p>
     *
     * @test
     */
    public function withIncorrectLengthInRequiredFields()
    {
        $this->markTestIncomplete('@TODO');
    }

    /**
     * <p>Creating a new rating with special characters into required fields</p>
     * <p>Steps:</p>
     * <p>1. Click button "Add New Rating"</p>
     * <p>2. Fill in fields in Review Details area by special characters</p>
     * <p>3. Click button "Save Rating"</p>
     * <p>Expected result:</p>
     * <p>Received the message that the rating has been saved.</p>
     *
     * @test
     */
    public function withSpecialCharacters()
    {
        $this->markTestIncomplete('@TODO');
    }

}
