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
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog product website resource model
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Website extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('catalog/product_website', 'product_id');
    }

    /**
     * Removes products from websites
     *
     * @param array $websiteIds
     * @param array $productIds
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Website
     */
    public function removeProducts($websiteIds, $productIds)
    {
        if ( !is_array($websiteIds)
             || !is_array($productIds)
             || count($websiteIds)==0
             || count($productIds)==0 ) {
            return $this;
        }

        $this->_getWriteAdapter()->beginTransaction();

        $this->_getWriteAdapter()->delete($this->getMainTable(),
            $this->_getWriteAdapter()->quoteInto(
                'website_id IN (?) ',
                $websiteIds
            ) . ' AND ' . $this->_getWriteAdapter()->quoteInto(
                'product_id IN(?)',
                $productIds
            )
        );

        $this->_getWriteAdapter()->commit();

        return $this;
    }

    /**
     * Add products to websites
     *
     * @param array $websiteIds
     * @param array $productIds
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Website
     */
    public function addProducts($websiteIds, $productIds)
    {
        if ( !is_array($websiteIds)
             || !is_array($productIds)
             || count($websiteIds)==0
             || count($productIds)==0 ) {
            return $this;
        }

        $this->_getWriteAdapter()->beginTransaction();

        // Before adding of products we should remove it old rows with same ids
        $this->removeProducts($websiteIds, $productIds);

        foreach ($productIds as $productId) {
            if ((int) $productId == 0) {
                continue;
            }
            foreach ($websiteIds as $websiteId) {
                $this->_getWriteAdapter()->insert($this->getMainTable(), array(
                    'product_id' => $productId,
                    'website_id' => $websiteId
                ));
            }
        }

        $this->_getWriteAdapter()->commit();

        return $this;
    }
} // Class Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Website End