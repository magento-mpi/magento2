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
 * @package     Mage_Catalog
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * API2 for product categories
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Api2_Products_Categories_Rest_Admin_V1 extends Mage_Catalog_Model_Api2_Products_Categories_Rest
{
    /**
     * Product category assign
     *
     * @param array $data
     * @return string
     */
    protected function _create(array $data)
    {
        $required = array('category_id');
        $notEmpty = array('category_id');
        $this->_validate($data, $required, $notEmpty);

        $productId = $this->getRequest()->getParam('id');
        /** @var $groupedProduct Mage_Catalog_Model_Product */
        $product = Mage::getModel('catalog/product')->setStoreId(0)->load($productId);
        if (!$product->getId()) {
            $this->_critical('Product not found', Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
        }

        $categoryId = $data['category_id'];
        /** @var $simpleProduct Mage_Catalog_Model_Category */
        $category = Mage::getModel('catalog/category')->setStoreId(0)->load($categoryId);
        if (!$category->getId()) {
            $this->_critical('Category not found', Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
        }

        $categoryIds = $product->getCategoryIds();
        if (!is_array($categoryIds)) {
            $categoryIds = array();
        }
        if (in_array($categoryId, $categoryIds)) {
            $this->_critical(sprintf('Product #%d is already assigned to category #%d',
                $productId, $categoryId), Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
        }
        $categoryIds[] = $categoryId;
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
     * Retrieve product data
     *
     * @return array
     */
    protected function _retrieve()
    {
        $return = array();

        $productId = $this->getRequest()->getParam('id');
        /** @var $groupedProduct Mage_Catalog_Model_Product */
        $product = Mage::getModel('catalog/product')->load($productId);
        if (!$product->getId()) {
            $this->_critical('Product not found', Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
        }

        $categoryIds = $product->getCategoryIds();
        foreach ($categoryIds as $categoryId) {
            $return[] = array('category_id' => $categoryId);
        }

        return $return;
    }

    /**
     * Get resource location
     *
     * @param Mage_Core_Model_Abstract $resource
     * @return string URL
     */
    protected function _getLocation(Mage_Core_Model_Abstract $resource)
    {
        /** @var $apiTypeRoute Mage_Api2_Model_Route_ApiType */
        $apiTypeRoute = Mage::getModel('api2/route_apiType');

        $instanceResourceType = $this->getConfig()->getResourceInstance($this->getResourceType());
        $chain = $apiTypeRoute->chain(
            new Zend_Controller_Router_Route($this->getConfig()->getMainRoute($instanceResourceType))
        );
        $params = array(
            'api_type' => $this->getRequest()->getApiType(),
            'id' => $this->getRequest()->getParam('id'),
            'categoryId' => $resource->getId()
        );
        $uri = $chain->assemble($params);

        return '/' . $uri;
    }
}
