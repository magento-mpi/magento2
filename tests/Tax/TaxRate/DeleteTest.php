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
 * Tax Rate deletion tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Tax_TaxRate_DeleteTest extends Mage_Selenium_TestCase
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
     * <p>Navigate to Sales->Tax->Manage Tax Zones&Rates</p>
     */
    protected function assertPreConditions()
    {
        // @TODO
    }

    /**
     * <p>Delete a Tax Rate</p>
     * <p>Steps:</p>
     * <p>1. Create a new Tax Rate</p>
     * <p>2. Open the Tax Rate</p>
     * <p>3. Delete the Tax Rate</p>
     * <p>Expected result:</p>
     * <p>Received the message that the Tax Rate has been deleted.</p>
     *
     * @test
     */
    public function notUsedInRule()
    {
        $this->markTestIncomplete('@TODO');
    }

    /**
     * <p>Delete a Tax Rate that used</p>
     * <p>Steps:</p>
     * <p>1. Create a new Tax Rate</p>
     * <p>2. Create a new Tax Rule that use Tax Rate from previous step</p>
     * <p>2. Open the Tax Rate</p>
     * <p>3. Delete the Tax Rate</p>
     * <p>Expected result:</p>
     * <p>Received the message that the Tax Rate could not be deleted.</p>
     *
     * @test
     */
    public function usedInRule()
    {
        $this->markTestIncomplete('@TODO');
    }
}
