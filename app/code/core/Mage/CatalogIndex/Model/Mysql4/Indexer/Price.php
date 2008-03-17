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
 * @package    Mage_CatalogIndex
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Price indexer resource model
 *
 * @author Sasha Boyko <alex.boyko@varien.com>
 */
class Mage_CatalogIndex_Model_Mysql4_Indexer_Price extends Mage_CatalogIndex_Model_Mysql4_Indexer_Abstract
{
    protected function _construct()
    {
        $this->_init('catalogindex/price', 'index_id');

        $this->_entityIdFieldName = 'entity_id';
        $this->_attributeIdFieldName = 'attribute_id';
        $this->_storeIdFieldName = 'store_id';
    }

    protected function _getReplaceCondition($data)
    {
        $conditions = array();

        if (isset($data[$this->_entityIdFieldName]))
            $conditions[] = $this->_getWriteAdapter()->quoteInto("{$this->_entityIdFieldName} = ?", $data[$this->_entityIdFieldName]);

        if (isset($data[$this->_storeIdFieldName]))
            $conditions[] = $this->_getWriteAdapter()->quoteInto("{$this->_storeIdFieldName} = ?", $data[$this->_storeIdFieldName]);

        if (isset($data[$this->_attributeIdFieldName]))
            $conditions[] = $this->_getWriteAdapter()->quoteInto("{$this->_attributeIdFieldName} = ?", $data[$this->_attributeIdFieldName]);

        return $conditions;
    }
}