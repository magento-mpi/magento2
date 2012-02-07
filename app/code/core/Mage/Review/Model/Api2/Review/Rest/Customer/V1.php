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
 * API2 for review item
 *
 * @category   Mage
 * @package    Mage_Review
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Review_Model_Api2_Review_Rest_Customer_V1 extends Mage_Review_Model_Api2_Review_Rest
{
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
            $this->_critical(self::RESOURCE_NOT_FOUND);
        }
        // check status and review store
        $storeId = $this->getRequest()->getParam('store');
        if ($storeId !== null) {
            $this->_validateStores(array($storeId));
            $isStoreEligible = in_array($storeId, $review->getStores());
        } else {
            $isStoreEligible = true;
        }
        $isReviewStatusEligible = ($review->getStatusId() == Mage_Review_Model_Review::STATUS_APPROVED);
        if (!$isReviewStatusEligible || !$isStoreEligible) {
            $this->_critical(self::RESOURCE_NOT_FOUND);
        }
        return $review;
    }

    /**
     * Review specific input data validation
     *
     * @throws Mage_Api2_Exception
     * @param array $data
     * @param array $required
     * @param array $notEmpty
     */
    protected function _validate(array $data, array $required = array(), array $notEmpty = array())
    {
        parent::_validate($data, $required, $notEmpty);
        if (isset($data['stores'])) {
            $this->_validateStores($data['stores']);
        }
        if (isset($data['status_id']) && !$this->_validator->isStatusValid($data['status_id'])) {
            $this->_critical('Invalid status provided', Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Validate stores
     *
     * @param mixed $stores
     */
    protected function _validateStores($stores)
    {
        if (!$this->_validator->areStoresValid($stores)) {
            $this->_critical('Invalid stores provided', Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
        }
    }
}
