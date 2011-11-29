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
 * Delete Website into Backend
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Store_Website_DeleteTest extends Mage_Selenium_TestCase
{

    /**
     * <p>Login to backend</p>
     */
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
    }

    /**
     * <p>Preconditions:</p>
     * <p>Navigate to System -> Manage Stores</p>
     */
    protected function assertPreConditions()
    {
        $this->navigate('manage_stores');
    }

    /**
     * <p>Delete Website without Store</p>
     * <p>Preconditions:</p>
     * <p>Website created without Store;</p>
     * <p>Steps:</p>
     * <p>1. Navigate to "System->Manage Stores";</p>
     * <p>2. Select created Website from the grid and open it;</p>
     * <p>3. Click "Delete Website" button;</p>
     * <p>4. Select "No" on Backup Options page;</p>
     * <p>5. Click "Delete Website" button;</p>
     * <p>Expected result:</p>
     * <p>Success message appears - "The website has been deleted."</p>
     *
     * @test
     */
    public function deleteWithoutStore()
    {
        $this->markTestIncomplete('@TODO');
    }

    /**
     * <p>Delete Website with Store</p>
     * <p>Preconditions:</p>
     * <p>Website created with Store;</p>
     * <p>Steps:</p>
     * <p>1. Navigate to "System->Manage Stores";</p>
     * <p>2. Select created Website from the grid and open it;</p>
     * <p>3. Click "Delete Website" button;</p>
     * <p>4. Select "No" on Backup Options page;</p>
     * <p>5. Click "Delete Website" button;</p>
     * <p>Expected result:</p>
     * <p>Success message appears - "The website has been deleted."</p>
     *
     * @test
     */
    public function deleteWithStore()
    {
        $this->markTestIncomplete('@TODO');
    }

    /**
     * <p>Delete Website with Store and Store View</p>
     * <p>Preconditions:</p>
     * <p>Website created with Store and Store View;</p>
     * <p>Steps:</p>
     * <p>1. Navigate to "System->Manage Stores";</p>
     * <p>2. Select created Website from the grid and open it;</p>
     * <p>3. Click "Delete Website" button;</p>
     * <p>4. Select "No" on Backup Options page;</p>
     * <p>5. Click "Delete Website" button;</p>
     * <p>Expected result:</p>
     * <p>Success message appears - "The website has been deleted."</p>
     *
     * @test
     */
    public function deleteWithStoreAndStoreView()
    {
        $this->markTestIncomplete('@TODO');
    }

    /**
     * <p>Delete Website with assigned product</p>
     * <p>Preconditions:</p>
     * <p>Create product and assign it to created Website</p>
     * <p>Steps:</p>
     * <p>1. Navigate to "System->Manage Stores";</p>
     * <p>2. Select created Website from the grid and open it;</p>
     * <p>3. Click "Delete Website" button;</p>
     * <p>4. Select "No" on Backup Options page;</p>
     * <p>5. Click "Delete Website" button;</p>
     * <p>Expected result:</p>
     * <p>Success message appears - "The website has been deleted."</p>
     *
     * @test
     */
    public function deleteWithAssignedProduct()
    {
        $this->markTestIncomplete('@TODO');
    }

}
