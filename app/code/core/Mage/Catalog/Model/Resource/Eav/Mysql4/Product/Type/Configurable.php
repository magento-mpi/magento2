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
 * Configurable product type resource model
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Type_Configurable extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('catalog/product_super_link', 'link_id');
    }

    public function saveProducts($mainProductId, $productIds)
    {
        $this->_getWriteAdapter()->delete($this->getMainTable(),
            $this->_getWriteAdapter()->quoteInto('parent_id=?', $mainProductId)
        );
        foreach ($productIds as $productId) {
        	$this->_getWriteAdapter()->insert($this->getMainTable(), array(
        	   'product_id'    => $productId,
        	   'parent_id'     => $mainProductId
        	));
        }
        return $this;
    }
}