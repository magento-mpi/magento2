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
 * Test verification of Search function.
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Enterprise2_Mage_Status_SearchTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Preconditions:</p>
     * <p>Navigate to System -> Order Statuses</p>
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('order_statuses');
    }
    
    /**
     * <p>Test verifies if all buttons are present on the page.</p>
     * <p>Steps:</p>
     * <p>1. Verify that 'Assign Status to State' button is present.</p>
     * <p>2. Verify that 'Create New Status' button is present.</p>
     * <p>3. Verify that 'Reset Filter' button is present.</p>
     * <p>4. Verify that 'Search' button is present.</p>
     *
     * @test
     */
    public function verifyOrderStatusButtons()
    {
        $this->assertTrue($this->controlIsPresent('button', 'assign_status_to_state'),
            'There is no "Assign Status to State" button on the page');
        $this->assertTrue($this->controlIsPresent('button', 'create_new_status'),
            'There is no "Create New Status" button on the page');
        $this->assertTrue($this->controlIsPresent('button', 'reset_filter'),
            'There is no "Reset Filter" button on the page');
        $this->assertTrue($this->controlIsPresent('button', 'search'), 'There is no "Search" button on the page');
      
    }
    
    /**
     * <p>Verifying how search works on Status correct partial value.</p>
     * <p>Steps:</p>
     * <p>1. Fill in 'Status' field with a part of correct value.</p>
     * <p>1. Click 'Search' button.</p>
     * <p>Expected result:</p>
     * <p>Statuses that match the search criteria display.</p>
     *
     * @test
     * @TestlinkId	TL-MAGE-3618
     */
    public function statusCorrectPartialValue()
    {
        $this->fillForm('search_status_part');
        $this->clickButton('search');
        $this->assertMessagePresent('success', 'success_search');
    }
    
    /**
     * <p>Verifying how search works on Status correct value.</p>
     * <p>Steps:</p>
     * <p>1. Fill in 'Status' field with correct value.</p>
     * <p>1. Click 'Search' button.</p>
     * <p>Expected result:</p>
     * <p>Statuses that match the search criteria display.</p>
     *
     * @test
     * @TestlinkId	TL-MAGE-3618
     */
    public function statusCorrectValue()
    {
        $this->fillForm('search_status');
        $this->clickButton('search');
        $this->assertMessagePresent('success', 'success_search');
    }
    
    /**
     * <p>Verifying how search works on Status correct value.</p>
     * <p>Steps:</p>
     * <p>1. Fill in 'Status' field with correct value.</p>
     * <p>1. Click 'Search' button.</p>
     * <p>Expected result:</p>
     * <p>Statuses that match the search criteria display.</p>
     *
     * @test
     * @TestlinkId	TL-MAGE-3618
     */
    public function statusIncorrectValue()
    {
        $this->fillForm('search_status_incorrect');
        $this->clickButton('search');
        $this->assertMessagePresent('success', 'search_no_result');
    }
}
