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
 * REST API for configurable product associated products resource (admin role)
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Api2_Product_Configurable_AssociatedProduct_Rest_Admin_V1
    extends Mage_Catalog_Model_Api2_Product_Configurable_AssociatedProduct_Rest
{
    /**
     * Assign products to the specified category
     *
     * @param array $data
     * @return string
     */
    protected function _create(array $data)
    {
        /** @var $validator Mage_Catalog_Model_Api2_Product_Validator_Product_Configurable_AssociatedProduct */
        $validator = Mage::getModel('Mage_Catalog_Model_Api2_Product_Validator_Product_Configurable_AssociatedProduct');
        if ($validator->isValidForCreate($this->_getProduct(), $data)) {
            $assignedProducts = array();
            $currentlyAssignedProducts = $this->_getAssociatedProductsIds();
            foreach ($currentlyAssignedProducts as $assignedProductId) {
                $assignedProducts[$assignedProductId] = $assignedProductId;
            }
            /** @var $productHelper Mage_Catalog_Helper_Product */
            $productHelper = Mage::helper('Mage_Catalog_Helper_Product');
            $productToBeAssigned = $productHelper->getProduct($data['product_id'], $this->_getStore()->getId());
            // the keys of the $assignedProducts array must be equal to the product ids
            $assignedProducts[$productToBeAssigned->getId()] = $productToBeAssigned->getId();
            $this->_saveAssociatedProducts($assignedProducts);
            return $this->_getSubresourceLocation($this->_getProduct(), $productToBeAssigned);
        } else {
            $this->_processValidationErrors($validator);
        }
    }

    /**
     * Get subresource location
     *
     * @param Mage_Catalog_Model_Product $configurableProduct
     * @param Mage_Catalog_Model_Product $associatedProduct
     * @return string URL
     */
    protected function _getSubresourceLocation($configurableProduct, $associatedProduct)
    {
        /* @var $apiTypeRoute Mage_Api2_Model_Route_ApiType */
        $apiTypeRoute = Mage::getModel('Mage_Api2_Model_Route_ApiType');

        $chain = $apiTypeRoute->chain(
            new Zend_Controller_Router_Route($this->getConfig()->getRouteWithEntityTypeAction($this->getResourceType()))
        );
        $params = array(
            'api_type'   => $this->getRequest()->getApiType(),
            'id'         => $configurableProduct->getId(),
            'product_id' => $associatedProduct->getId()
        );
        $uri = $chain->assemble($params);

        return '/' . $uri;
    }

    /**
     * Retrieve the list of products assigned to the configurable one
     *
     * @return array
     */
    protected function _retrieveCollection()
    {
        $assignedProducts = array();
        /** @var $validator Mage_Catalog_Model_Api2_Product_Validator_Product_Configurable_AssociatedProduct */
        $validator = Mage::getModel('Mage_Catalog_Model_Api2_Product_Validator_Product_Configurable_AssociatedProduct');
        if ($validator->isValidForMultiGet($this->_getProduct())) {
            $assignedProductsIds = $this->_getAssociatedProductsIds();
            foreach ($assignedProductsIds as $id) {
                $assignedProducts[] = array('product_id' => $id);
            }
        } else {
            $this->_processValidationErrors($validator);
        }
        return $assignedProducts;
    }

    /**
     * Unassign the specified product from the configurable one
     */
    protected function _delete()
    {
        $associatedProductId = $this->getRequest()->getParam('product_id');
        /** @var $associatedProduct Mage_Catalog_Model_Product */
        $associatedProduct = Mage::getModel('Mage_Catalog_Model_Product')->load($associatedProductId);
        /** @var $validator Mage_Catalog_Model_Api2_Product_Validator_Product_Configurable_AssociatedProduct */
        $validator = Mage::getModel('Mage_Catalog_Model_Api2_Product_Validator_Product_Configurable_AssociatedProduct');
        if ($validator->isValidForDelete($this->_getProduct(), $associatedProduct)) {
            $assignedProductsIds = array_flip($this->_getAssociatedProductsIds());
            unset($assignedProductsIds[$associatedProduct->getId()]);
            $this->_saveAssociatedProducts($assignedProductsIds);
        } else {
            $this->_processValidationErrors($validator);
        }
    }

    /**
     * Retrieve the list of associated products' IDs
     *
     * @return array
     */
    protected function _getAssociatedProductsIds()
    {
        /** @var $configurableType Mage_Catalog_Model_Product_Type_Configurable */
        $configurableType = $this->_getProduct()->getTypeInstance();
        $assignedProductsIds = $configurableType->getUsedProductIds($this->_getProduct());
        return $assignedProductsIds;
    }

    /**
     * Save configurable associated products
     *
     * @param array $assignedProducts
     */
    protected function _saveAssociatedProducts(array $assignedProducts)
    {
        $this->_getProduct()->setConfigurableProductsData($assignedProducts);
        try {
            $this->_getProduct()->save();
        } catch (Mage_Core_Exception $e) {
            $this->_critical($e->getMessage(), Mage_Api2_Model_Server::HTTP_INTERNAL_ERROR);
        } catch (Exception $e) {
            $this->_critical(self::RESOURCE_UNKNOWN_ERROR);
        }
    }
}
