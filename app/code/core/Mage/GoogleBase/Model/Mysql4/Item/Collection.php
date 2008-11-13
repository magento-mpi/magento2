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
 * @package    Mage_GoogleBase
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Google Base items collection
 *
 * @category   Mage
 * @package    Mage_GoogleBase
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_GoogleBase_Model_Mysql4_Item_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
	protected function _construct()
	{
		$this->_init('googlebase/item');
	}

    protected function _initSelect()
    {
        parent::_initSelect();
        $this->_joinTables();
        return $this;
    }

    public function addStoreFilterId($storeId)
    {
        $this->getSelect()->where('main_table.store_id=?', $storeId);
        return $this;
    }

    public function addFieldToFilter($field, $condition=null)
    {
        if ($field == 'name') {
            $conditionSql = $this->_getConditionSql('p.value', $condition);
            $this->getSelect()->where($conditionSql);
        } else {
            parent::addFieldToFilter($field, $condition);
        }
    }

    protected function _joinTables()
    {
        $entityType = Mage::getSingleton('eav/config')->getEntityType('catalog_product');
        $attribute = Mage::getModel('eav/config')->getAttribute($entityType->getEntityTypeId(),'name');
        $table = $attribute->getBackend()->getTable();
        $joinCondition = sprintf('p.entity_type_id=%d
            AND p.attribute_id=%d
            AND main_table.product_id=p.entity_id',
//            AND p.store_id=main_table.store_id
            $entityType->getEntityTypeId(),
            $attribute->getAttributeId()
        );

        $this->getSelect()
            ->join(
                array('p' => $attribute->getBackend()->getTable()),
                $joinCondition,
                array('name' => 'p.value'));

        $this->getSelect()
            ->joinLeft(
                array('types' => $this->getTable('googlebase/types')),
                'main_table.type_id=types.type_id');

        return $this;
    }
}