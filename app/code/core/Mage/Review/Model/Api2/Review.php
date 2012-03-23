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
 * Abstract Api2 model for review item
 *
 * @category   Mage
 * @package    Mage_Review
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Review_Model_Api2_Review extends Mage_Api2_Model_Resource
{
    /**
     * Get available attributes of API resource
     *
     * @param string $userType
     * @param string $operation
     * @return array
     */
    public function getAvailableAttributes($userType, $operation)
    {
        $available     = $this->getAvailableAttributesFromConfig();
        $excludedAttrs = $this->getExcludedAttributes($userType, $operation);
        $includedAttrs = $this->getIncludedAttributes($userType, $operation);
        $resourceAttrs = $this->_getResourceAttributes();

        // if resource returns not-associative array - attributes' codes only
        if (0 === key($resourceAttrs)) {
            $resourceAttrs = array_combine($resourceAttrs, $resourceAttrs);
        }
        foreach ($resourceAttrs as $attrCode => $attrLabel) {
            if (!isset($available[$attrCode])) {
                $available[$attrCode] = empty($attrLabel) ? $attrCode : $attrLabel;
            }
        }
        foreach (array_keys($available) as $code) {
            if (in_array($code, $excludedAttrs) || ($includedAttrs && !in_array($code, $includedAttrs))) {
                unset($available[$code]);
            }
        }
        return $available;
    }

    /**
     * Load review status by code
     *
     * @param string $code
     * @return Mage_Review_Model_Review_Status
     */
    protected function _loadStatusByCode($code)
    {
        /** @var $status Mage_Review_Model_Review_Status */
        $status = Mage::getModel('review/review_status')->load($code, 'status_code');
        return $status;
    }

    /**
     * Load review by id
     *
     * @param int $id
     * @throws Mage_Api2_Exception
     * @return Mage_Review_Model_Review
     */
    protected function _loadReviewById($id)
    {
        /** @var $review Mage_Review_Model_Review */
        $review = Mage::getModel('review/review')->load($id);
        if (!$review->getId() || $review->getEntityPkValue() != $this->getRequest()->getParam('product_id')) {
            $this->_critical(self::RESOURCE_NOT_FOUND);
        }
        return $review;
    }

    /**
     * Load product by id
     *
     * @param int $id
     * @throws Mage_Api2_Exception
     * @return Mage_Catalog_Model_Product
     */
    protected function _loadProductById($id)
    {
        /** @var $product Mage_Catalog_Model_Product */
        $product = Mage::getModel('catalog/product')->load($id);
        if (!$product->getId()) {
            $this->_critical('Product not found.', Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
        }
        return $product;
    }
}
