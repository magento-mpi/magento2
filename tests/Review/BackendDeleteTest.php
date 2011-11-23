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
 * Delete review into backend
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Review_DeleteTest extends Mage_Selenium_TestCase
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
     * <p>Navigate to Catalog -> Reviews and Ratings -> Customer Reviews -> All Reviews</p>
     */
    protected function assertPreConditions()
    {
        $this->navigate('all_reviews');
        $this->addParameter('storeId', '1');
    }

    /**
     * <p>Delete review with Rating created</p>
     *
     * <p>Preconditions:</p>
     * <p>Review with rating created;</p>
     * <p>Rating created</p>
     * <p>Steps:</p>
     * <p>1. Select created review from the list and open it;</p>
     * <p>2. Click "Delete Review" button;</p>
     * <p>Expected result:</p>
     * <p>Success message appears - review removed from the list</p>
     */
    public function deleteWithRating()
    {
        $this->markTestIncomplete('@TODO');
    }

    /**
     * <p>Delete review with Rating created</p>
     *
     * <p>Preconditions:</p>
     * <p>Review with store view created;</p>
     * <p>Store view created</p>
     * <p>Steps:</p>
     * <p>1. Select created review from the list and open it;</p>
     * <p>2. Click "Delete Review" button;</p>
     * <p>Success message appears - review removed from the list</p>
     */
    public function deleteWithVisibleIn()
    {
        $this->markTestIncomplete('@TODO');
    }

    /**
     * <p>Delete review using Mass-Action</p>
     *
     * <p>Preconditions:</p>
     * <p>Review created;</p>
     * <p>Steps:</p>
     * <p>1. Select created review from the list check it;</p>
     * <p>2. Select "Delete" in Actions;</p>
     * <p>3. Click "Submit" button;</p>
     * <p>Success message appears - review removed from the list</p>
     */
    public function deleteMassAction()
    {
        $this->markTestIncomplete('@TODO');
    }



}
