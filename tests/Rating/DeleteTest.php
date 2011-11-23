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
 * Delete Rating in Backend
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Rating_DeleteTest extends Mage_Selenium_TestCase
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
     * <p>Delete rating that is used in Review</p>
     * <p>Preconditions:</p>
     * <p>Rating created</p>
     * <p>Review created using Rating</p>
     * <p>Steps:</p>
     * <p>1. Open created rating;</p>
     * <p>2. Click "Delete" button;</p>
     * <p>Expected result:</p>
     * <p>Success message appears - Rating removed from the list</p>
     *
     * <p>Verification:</p>
     * <p>1. Navigate to Catalog -> Reviews and Ratings -> Customer Reviews -> All Reviews;</p>
     * <p>2. Select created Review from the list and open it;</p>
     * <p>3. Verify that Rating is absent in review;</p>
     *
     * @test
     */
    public function deleteRatingUsedInReview()
    {
        $this->markTestIncomplete('@TODO');
    }

    /**
     * <p>Delete rating</p>
     * <p>Preconditions:</p>
     * <p>Rating created</p>
     * <p>Steps:</p>
     * <p>1. Open created rating;</p>
     * <p>2. Click "Delete" button;</p>
     * <p>Expected result:</p>
     * <p>Success message appears - Rating removed from the list</p>
     *
     * @test
     */
    public function deleteRatingNotUsedInReview()
    {
        $this->markTestIncomplete('@TODO');
    }

}
