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
        } catch (Mage_Core_Exception $e) {
            $this->_error($e->getMessage(), Mage_Api2_Model_Server::HTTP_INTERNAL_ERROR);
        } catch (Exception $e) {
            $this->_critical(self::RESOURCE_INTERNAL_ERROR);
        }
    }

    /**
     * Update specified review item
     *
     * @param array $data
     * @throws Mage_Api2_Exception
     */
    protected function _update(array $data)
    {
        $notEmpty = array('status_id', 'stores', 'nickname', 'title', 'detail');
        $this->_validate($data, array(), $notEmpty);

        $review = $this->_loadReview();
        $review->addData($data);
        if (isset($data['stores'])) {
            $review->setStores($data['stores']);
        }
        try {
            $review->save();
        } catch (Mage_Core_Exception $e) {
            $this->_error($e->getMessage(), Mage_Api2_Model_Server::HTTP_INTERNAL_ERROR);
        } catch (Exception $e) {
            $this->_critical(self::RESOURCE_INTERNAL_ERROR);
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

        $validStatusList = array();
        $statusList = Mage::getModel('review/review')
            ->getStatusCollection()
            ->load()
            ->toArray();

        foreach ($statusList['items'] as $status) {
            $validStatusList[] = $status['status_id'];
        }
        if (isset($data['status_id']) && !in_array($data['status_id'], $validStatusList)) {
            $this->_critical('Invalid status provided', Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
        }

        if (isset($data['stores']) && !is_array($data['stores'])) {
            $this->_critical('Invalid stores provided', Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
        }
        $validStores = array();
        foreach (Mage::app()->getStores(true) as $store) {
            $validStores[] = $store->getId();
        }
        foreach ($data['stores'] as $store) {
            if (!in_array($store, $validStores)) {
                $this->_critical(sprintf('Invalid store ID "%s" provided', $store),
                    Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
            }
        }
    }
}
