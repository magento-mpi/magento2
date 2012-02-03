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
 * @category    Mage
 * @package     Mage_Review
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Api2 for review item
 *
 * @category   Mage
 * @package    Mage_Review
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Review_Model_Api2_Review_Rest_Admin_V1 extends Mage_Review_Model_Api2_Review_Rest
{
    /**
     * Retrieve information about specified review item
     *
     * @throws Mage_Api2_Exception
     * @return array
     */
    protected function _retrieve()
    {
        $review = $this->_loadReview();
        return $review->getData();
    }

    /**
     * Delete specified review item
     *
     * @throws Mage_Api2_Exception
     */
    protected function _delete()
    {
        $review = $this->_loadReview();
        try {
            $review->delete();
        } catch (Exception $e) {
            $this->_critical(Mage_Api2_Model_Resource::RESOURCE_INTERNAL_ERROR);
        }
    }

    /**
     * Load review by its id passed through request
     *
     * @throws Mage_Api2_Exception
     * @return Mage_Review_Model_Review
     */
    protected function _loadReview()
    {
        $reviewId = $this->getRequest()->getParam('id');
        /** @var $review Mage_Review_Model_Review */
        $review = Mage::getModel('review/review')->load($reviewId);
        if (!$review->getId()) {
            $this->_critical(Mage_Api2_Model_Resource::RESOURCE_NOT_FOUND);
        }
        return $review;
    }
}
