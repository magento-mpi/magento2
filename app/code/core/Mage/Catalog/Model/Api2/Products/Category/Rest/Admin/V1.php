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
class Mage_Catalog_Model_Api2_Products_Category_Rest_Admin_V1 extends Mage_Catalog_Model_Api2_Products_Category_Rest
{
    /**
     * Product category unassign
     */
    protected function _delete()
    {
        $productId = $this->getRequest()->getParam('id');
        /** @var $groupedProduct Mage_Catalog_Model_Product */
        $product = Mage::getModel('catalog/product')->load($productId);
        if (!$product->getId()) {
            $this->_critical('Product not found', Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
        }

        $categoryId = $this->getRequest()->getParam('categoryId');
        /** @var $simpleProduct Mage_Catalog_Model_Category */
        $category = Mage::getModel('catalog/category')->load($categoryId);
        if (!$category->getId()) {
            $this->_critical('Category not found', Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
        }

        $categoryIds = $product->getCategoryIds();
        if (!is_array($categoryIds)) {
            $categoryIds = array();
        }
        if (!in_array($categoryId, $categoryIds)) {
            $this->_critical(sprintf('Product #%d isn\'t assigned to category #%d',
                $productId, $categoryId), Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
        }
        foreach ($categoryIds as $key => $value) {
            if ($value == $categoryId) {
                unset($categoryIds[$key]);
                break;
            }
        }
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
}
