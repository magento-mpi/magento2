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
 * @category   Mage
 * @package    Mage_CatalogSearch
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Mage_CatalogSearch_Model_Mysql4_Fulltext_Collection
    extends Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection
{
    /**
     * Add search query filter
     *
     * @param   string $query
     * @return  Mage_CatalogSearch_Model_Mysql4_Search_Collection
     */
    public function addSearchFilter($query)
    {
        $this->addFieldToFilter('entity_id', array('in' => $this->_getSearchEntityIds($query)));
        return $this;
    }

    protected function _getSearchEntityIds($query)
    {
        $matchCondition = $this->getConnection()->quoteInto('MATCH (`data_index`) AGAINST (?)', $query);
        $select = $this->getConnection()->select()
            ->from(
                array('main' => $this->getTable('catalogsearch/fulltext')),
                array('product_id', 'relev' => new Zend_Db_Expr($matchCondition))
            )
            ->where($matchCondition)
            ->where('main.store_id=?', $this->getStoreId())
            ->order('relev DESC');
        return $this->getConnection()->fetchCol($select);
    }



}