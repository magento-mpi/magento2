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
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * API2 Review Validator
 *
 * @category   Mage
 * @package    Mage_Review
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Review_Model_Api2_Validator extends Mage_Api2_Model_Resource_Validator_Fields
{
    /**
     * Check if passed review status is valid
     *
     * @param string $statusCode
     * @return bool
     */
    protected function _isStatusValid($statusCode)
    {
        /** @var $review Mage_Review_Model_Api2_Review */
        $review = Mage::getModel('review/review_status')->load($statusCode, 'status_code');
        if (null === $review->getId()) {
            $this->_addError('Invalid status provided.');
            return false;
        }
        return true;
    }

    /**
     * Check if passed stores are valid
     *
     * @param array $stores
     * @return bool
     */
    protected function _areStoresValid($stores)
    {
        if (!is_array($stores)) {
            $this->_addError('Invalid stores provided.');
            return false;
        }

        $systemStores = array_keys(Mage::app()->getStores(true));
        foreach ($stores as $storeId) {
            if (!is_numeric($storeId) || !in_array($storeId, $systemStores)) {
                $this->_addError('Invalid stores provided.');
                return false;
            }
        }
        return true;
    }

    protected function _isDetailedRatingValid($data)
    {
        if (!array_key_exists('detailed_rating', $data)) {
            $this->_addError('Invalid detailed_rating data provided.');
            return false;
        }

        //TODO validate rating names
    }

    /**
     * Validate data.
     * If fails validation, then this method returns false, and
     * getErrors() will return an array of errors that explain why the
     * validation failed.
     *
     * @param array $data
     * @return bool
     */
    public function isValidDataForReviewUpdate(array $data)
    {
        if (parent::isValidData($data, true)) {
            if (isset($data['status'])) {
                $this->_isStatusValid($data['status']);
            };
        }

        if (array_key_exists('stores', $data)) {
            $this->_areStoresValid($data['stores']);
        }

        return !count($this->getErrors());
    }

    /**
     * Valdate data
     *
     * @param array $data
     * @return bool
     */
    public function isValidDataForCreateByAdmin(array $data)
    {
        if (parent::isValidData($data)) {
            if (isset($data['status'])) {
                $this->_isStatusValid($data['status']);
            };
            if (isset($data['stores'])) {
                $this->_areStoresValid($data['stores']);
            }
        }
        return !count($this->getErrors());
    }

    /**
     * Valdate data
     *
     * @param array $data
     * @return bool
     */
    public function isValidDataForCreateByCustomer(array $data)
    {
        $skipped = array('status');
        $isValid = true;

        // required fields
        if (count($this->_requiredFields) > 0) {
            $notEmptyValidator = new Zend_Validate_NotEmpty();
            foreach ($this->_requiredFields as $requiredField) {
                $value = isset($data[$requiredField]) ? $data[$requiredField] : null;
                if (!in_array($requiredField, $skipped) &&
                        !$notEmptyValidator->isValid($value)) {
                    $isValid = false;
                    foreach ($notEmptyValidator->getMessages() as $message) {
                        $this->_addError(sprintf('%s: %s', $requiredField, $message));
                    }
                }
            }
        }

        // fields rules
        foreach ($data as $field => $value) {
            if (!in_array($field, $skipped) && isset($this->_validators[$field])) {
                /* @var $validator Zend_Validate_Interface */
                $validator = $this->_validators[$field];
                if (!$validator->isValid($value)) {
                    $isValid = false;
                    foreach ($validator->getMessages() as $message) {
                        $this->_addError(sprintf('%s: %s', $field, $message));
                    }
                }
            }
        }

        if ($this->_isDetailedRatingValid($data)) {
            $isValid = false;
        }

        if (!isset($data['stores'])) {
            $isValid = false;
            $this->_addError('Missed stores data.');
        }

        if ($isValid) {
            $this->_areStoresValid($data['stores']);
        }

        return $isValid && !count($this->getErrors());
    }

    /**
     * Valdate data
     *
     * @param array $data
     * @return bool
     */
    public function isValidDataForCreateByGuest(array $data)
    {
        return $this->isValidDataForCreateByCustomer($data);
    }
}
