<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * API2 for product categories
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Api2_Product_Category_Rest_Admin_V1 extends Mage_Catalog_Model_Api2_Product_Category_Rest
{
    /**
     * Product category assign
     *
     * @param array $data
     * @return string
     */
    protected function _create(array $data)
    {
        /* @var $validator Mage_Api2_Model_Resource_Validator_Fields */
        $validator = Mage::getResourceModel('Mage_Api2_Model_Resource_Validator_Fields',
            array('options' =>
                array('resource' => $this)
        ));
        if (!$validator->isValidData($data)) {
            foreach ($validator->getErrors() as $error) {
                $this->_error($error, Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
            }
            $this->_critical(self::RESOURCE_DATA_PRE_VALIDATION_ERROR);
        }

        $product = $this->_getProduct();
        $category = $this->_getCategoryById($data['category_id']);

        $categoryIds = $product->getCategoryIds();
        if (!is_array($categoryIds)) {
            $categoryIds = array();
        }
        if (in_array($category->getId(), $categoryIds)) {
            $this->_critical(sprintf('Product #%d is already assigned to category #%d',
                $product->getId(), $category->getId()), Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
        }
        if ($category->getId() == Mage_Catalog_Model_Category::TREE_ROOT_ID) {
            $this->_critical('Cannot assign product to tree root category.', Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
        }
        $categoryIds[] = $category->getId();
        $product->setCategoryIds(implode(',', $categoryIds));

        try{
            $product->save();
        } catch (Mage_Core_Exception $e) {
            $this->_critical($e->getMessage(), Mage_Api2_Model_Server::HTTP_INTERNAL_ERROR);
        } catch (Exception $e) {
            $this->_critical(self::RESOURCE_INTERNAL_ERROR);
        }

        return $this->_getLocation($category);
    }

    /**
     * Product category unassign
     *
     * @return bool
     */
    protected function _delete()
    {
        $product = $this->_getProduct();
        $category = $this->_getCategoryById($this->getRequest()->getParam('category_id'));

        $categoryIds = $product->getCategoryIds();
        $categoryToBeDeletedId = array_search($category->getId(), $categoryIds);
        if (false === $categoryToBeDeletedId) {
            $this->_critical(sprintf('Product #%d isn\'t assigned to category #%d',
                $product->getId(), $category->getId()), Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
        }

        // delete category
        unset($categoryIds[$categoryToBeDeletedId]);
        $product->setCategoryIds(implode(',', $categoryIds));

        try{
            $product->save();
        } catch (Mage_Core_Exception $e) {
            $this->_critical($e->getMessage(), Mage_Api2_Model_Server::HTTP_INTERNAL_ERROR);
        } catch (Exception $e) {
            $this->_critical(self::RESOURCE_INTERNAL_ERROR);
        }

        return true;
    }

    /**
     * Return all assigned categories
     *
     * @return array
     */
    protected function _getCategoryIds()
    {
        return $this->_getProduct()->getCategoryIds();
    }

    /**
     * Get resource location
     *
     * @param Mage_Core_Model_Abstract $resource
     * @return string URL
     */
    protected function _getLocation($resource)
    {
        /** @var $apiTypeRoute Mage_Api2_Model_Route_ApiType */
        $apiTypeRoute = Mage::getModel('Mage_Api2_Model_Route_ApiType');

        $chain = $apiTypeRoute->chain(new Zend_Controller_Router_Route(
            $this->getConfig()->getRouteWithEntityTypeAction($this->getResourceType())
        ));
        $params = array(
            'api_type' => $this->getRequest()->getApiType(),
            'id' => $this->getRequest()->getParam('id'),
            'category_id' => $resource->getId()
        );
        $uri = $chain->assemble($params);

        return '/' . $uri;
    }
}
