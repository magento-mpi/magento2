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
 * Abstract API2 class for product website resource
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Catalog_Model_Api2_Product_Website_Rest extends Mage_Catalog_Model_Api2_Product_Website
{
    /**
     * Product website retrieve is not available
     */
    protected function _retrieve()
    {
        $this->_critical(self::RESOURCE_METHOD_NOT_ALLOWED);
    }

    /**
     * Get product websites list
     *
     * @return array
     */
    protected function _retrieveCollection()
    {
        $return = array();
        foreach ($this->_getWebsiteStoreIds() as $websiteId) {
            $return[] = array('website_id' => $websiteId);
        }
        return $return;
    }

    /**
     * Product websites update is not available
     *
     * @param array $data
     */
    protected function _update(array $data)
    {
        $this->_critical(self::RESOURCE_METHOD_NOT_ALLOWED);
    }

    /**
     * Customer does not have permissions for product category unassign
     */
    protected function _delete()
    {
        $this->_critical(self::RESOURCE_METHOD_NOT_ALLOWED);
    }

    /**
     * Get website store ids
     *
     * @return array
     */
    protected function _getWebsiteStoreIds()
    {
        /* @var $product Mage_Catalog_Model_Product */
        $product = $this->_loadProductById($this->getRequest()->getParam('id'));
        $websiteIds = $product->getWebsiteStoreIds()->addIsActiveFilter()->getAllIds();
        return $websiteIds;
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
        /* @var $product Mage_Catalog_Model_Product */
        $product = Mage::getModel('catalog/product')->load($id);
        if (!$product->getId()) {
            $this->_critical(sprintf('Product not found #%s.', $id), Mage_Api2_Model_Server::HTTP_NOT_FOUND);
        }
        return $product;
    }
}
