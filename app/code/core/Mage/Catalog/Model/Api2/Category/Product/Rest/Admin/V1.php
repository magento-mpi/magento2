<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Api2
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * REST API for product categories resource (admin role)
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Api2_Category_Product_Rest_Admin_V1 extends Mage_Catalog_Model_Api2_Category_Product_Rest
{
    /**
     * Retrieve list of products assigned to specified category
     *
     * @return array
     */
    protected function _retrieveCollection()
    {
        $assignedProducts = array();
        foreach ($this->_getCategory()->getProductsPosition() as $productId => $position) {
            $assignedProducts[] = array('product_id' => $productId, 'position' => $position);
        }
        return $assignedProducts;
    }

    /**
     * Assign products to specified category
     *
     * @param array $data
     */
    protected function _create(array $data)
    {
        /* @var $validator Mage_Catalog_Model_Api2_Category_Validator_Category_Product */
        $validator = Mage::getModel('Mage_Catalog_Model_Api2_Category_Validator_Category_Product');
        if ($validator->isValidForCreate($data, $this->_getCategory())) {
            $assignedProducts = $this->_getCategory()->getProductsPosition();
            $positionInCategory = isset($data['position']) ? intval($data['position']) : 0;
            $assignedProducts[$data['product_id']] = $positionInCategory;
            $this->_getCategory()->setPostedProducts($assignedProducts);
            try {
                $this->_getCategory()->save();
            } catch (Mage_Core_Exception $e) {
                $this->_critical($e->getMessage(), Mage_Api2_Model_Server::HTTP_INTERNAL_ERROR);
            } catch (Exception $e) {
                $this->_critical(self::RESOURCE_UNKNOWN_ERROR);
            }
        } else {
            $this->_processValidationErrors($validator);
        }
    }

    /**
     * Update position of the specified product in the specified category
     *
     * @param array $data
     */
    protected function _update(array $data)
    {
        $productId = $this->getRequest()->getParam('product_id');
        /* @var $validator Mage_Catalog_Model_Api2_Category_Validator_Category_Product */
        $validator = Mage::getModel('Mage_Catalog_Model_Api2_Category_Validator_Category_Product');
        if ($validator->isValidForUpdate($data, $productId, $this->_getCategory())) {
            $assignedProducts = $this->_getCategory()->getProductsPosition();
            $assignedProducts[$productId] = intval($data['position']);
            $this->_getCategory()->setPostedProducts($assignedProducts);
            try {
                $this->_getCategory()->save();
            } catch (Mage_Core_Exception $e) {
                $this->_critical($e->getMessage(), Mage_Api2_Model_Server::HTTP_INTERNAL_ERROR);
            } catch (Exception $e) {
                $this->_critical(self::RESOURCE_UNKNOWN_ERROR);
            }
        } else {
            $this->_processValidationErrors($validator);
        }
    }

    /**
     * Unassign product from specified category
     */
    protected function _delete()
    {
        $productId = $this->getRequest()->getParam('product_id');
        /* @var $validator Mage_Catalog_Model_Api2_Category_Validator_Category_Product */
        $validator = Mage::getModel('Mage_Catalog_Model_Api2_Category_Validator_Category_Product');
        if ($validator->isValidForDelete($productId, $this->_getCategory())) {
            $assignedProducts = $this->_getCategory()->getProductsPosition();
            unset($assignedProducts[$productId]);
            $this->_getCategory()->setPostedProducts($assignedProducts);
            try {
                $this->_getCategory()->save();
            } catch (Mage_Core_Exception $e) {
                $this->_critical($e->getMessage(), Mage_Api2_Model_Server::HTTP_INTERNAL_ERROR);
            } catch (Exception $e) {
                $this->_critical(self::RESOURCE_UNKNOWN_ERROR);
            }
        } else {
            $this->_processValidationErrors($validator);
        }
    }
}
