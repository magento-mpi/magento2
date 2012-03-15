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
 * API2 for product website resource
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Api2_Product_Website_Rest_Admin_V1 extends Mage_Catalog_Model_Api2_Product_Website_Rest
{
    /**
     * Get website store ids
     *
     * @return array
     */
    protected function _getWebsiteStoreIdsForRetrieveCollection()
    {
        return $this->_loadProductById($this->getRequest()->getParam('product_id'))->getWebsiteIds();
    }

    /**
     * Product website assign
     *
     * @param array $data
     * @return string
     */
    protected function _create(array $data)
    {
        /* @var $product Mage_Catalog_Model_Product */
        $product = $this->_loadProductById($this->getRequest()->getParam('product_id'));

        /* @var $validator Mage_Catalog_Model_Api2_Product_Website_Validator_Admin_Website */
        $validator = Mage::getModel('catalog/api2_product_website_validator_admin_website');
        if (!$validator->isValidDataForWebsiteAssignmentToProduct($product, $data)) {
            foreach ($validator->getErrors() as $error) {
                $this->_error($error, Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
            }
            $this->_critical(self::RESOURCE_DATA_PRE_VALIDATION_ERROR);
        }

        $websiteIds = $product->getWebsiteIds();
        /* @var $website Mage_Core_Model_Website */
        $website = Mage::getModel('core/website')->load($data['website_id']);
        $websiteIds[] = $website->getId(); // Existence of a website is checked in the validator
        $product->setWebsiteIds($websiteIds);

        try{
            $product->save();

            /**
             * Do copying data to stores
             */
            if (isset($data['copy_to_stores'])) {
                foreach ($data['copy_to_stores'] as $storeData) {
                    Mage::getModel('catalog/product')
                        ->setStoreId($storeData['store_from'])
                        ->load($product->getId())
                        ->setStoreId($storeData['store_to'])
                        ->save();
                }
            }

        } catch (Mage_Core_Exception $e) {
            $this->_critical($e->getMessage(), Mage_Api2_Model_Server::HTTP_INTERNAL_ERROR);
        } catch (Exception $e) {
            $this->_critical(self::RESOURCE_INTERNAL_ERROR);
        }

        return $this->_getLocation($website, $product);
    }

    /**
     * Product website unassign
     */
    protected function _delete()
    {
        /* @var $product Mage_Catalog_Model_Product */
        $product = $this->_loadProductById($this->getRequest()->getParam('product_id'));

        /* @var $website Mage_Core_Model_Website */
        $website = $this->_loadWebsiteById($this->getRequest()->getParam('website_id'));

        /* @var $validator Mage_Catalog_Model_Api2_Product_Website_Validator_Admin_Website */
        $validator = Mage::getModel('catalog/api2_product_website_validator_admin_website');
        if (!$validator->isWebsiteIsAssignmenToProduct($website, $product)) {
            foreach ($validator->getErrors() as $error) {
                $this->_error($error, Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
            }
            $this->_critical(self::RESOURCE_DATA_PRE_VALIDATION_ERROR);
        }

        $websiteIds = $product->getWebsiteIds();
        // Existence of a key is checked in the validator
        unset($websiteIds[array_search($website->getId(), $websiteIds)]);
        $product->setWebsiteIds($websiteIds);

        try {
            $product->save();
        } catch (Mage_Core_Exception $e) {
            $this->_critical($e->getMessage(), Mage_Api2_Model_Server::HTTP_INTERNAL_ERROR);
        } catch (Exception $e) {
            $this->_critical(self::RESOURCE_INTERNAL_ERROR);
        }
    }

    /**
     * Load product by id
     *
     * @param int $id
     * @throws Mage_Api2_Exception
     * @return Mage_Core_Model_Website
     */
    protected function _loadWebsiteById($id)
    {
        /* @var $website Mage_Core_Model_Website */
        $website = Mage::getModel('core/website')->load($id);
        if (!$website->getId()) {
            $this->_critical(sprintf('Website not found #%s.', $id), Mage_Api2_Model_Server::HTTP_NOT_FOUND);
        }
        return $website;
    }

    /**
     * Get resource location
     *
     * @param Mage_Core_Model_Website $website
     * @param Mage_Catalog_Model_Product $product
     * @return string URL
     */
    protected function _getLocation($website, $product)
    {
        /* @var $apiTypeRoute Mage_Api2_Model_Route_ApiType */
        $apiTypeRoute = Mage::getModel('api2/route_apiType');

        $chain = $apiTypeRoute->chain(
            new Zend_Controller_Router_Route($this->getConfig()->getRouteWithEntityTypeAction($this->getResourceType()))
        );
        $params = array(
            'api_type' => $this->getRequest()->getApiType(),
            'product_id' => $product->getId(),
            'website_id' => $website->getId()
        );
        $uri = $chain->assemble($params);

        return '/' . $uri;
    }
}
