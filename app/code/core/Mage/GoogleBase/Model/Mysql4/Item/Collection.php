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
class Mage_GoogleBase_Model_Mysql4_Item_Collection extends Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection
{
    protected function _construct() 
    {
    	parent::_construct();
    	$this->setRowIdFieldName('item_id');
    }
    
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->_joinTables();
        return $this;
    }

    public function addStoreFilterId($storeId)
    {
        $this->getSelect()->where('items.store_id=?', $storeId);
        return $this;
    }

    public function setOrder($attribute, $dir='desc')
    {
        if (in_array($attribute, $this->_getAllJoinFeilds())) {
            $this->getSelect()->order($attribute . ' ' . $dir);
        } else {
            parent::setOrder($attribute, $dir);
        }
        return $this;
    }

    public function addAttributeToFilter($attribute, $condition=null, $joinType='inner')
    {
        if (in_array($attribute, $this->_getAllJoinFeilds())) {
            $conditionSql = $this->_getConditionSql($attribute, $condition);
            $this->getSelect()->where($conditionSql);
        } else {
            parent::addAttributeToFilter($attribute, $condition, $joinType);
        }
        return $this;
    }

    protected function _joinTables()
    {
        $this->addAttributeToSelect('name');

        $this->getSelect()
            ->join(
                array('items' => $this->getTable('googlebase/items')),
                'e.entity_id=items.product_id',
                $this->_getJoinFields('items'))
            ->joinLeft(
                array('types' => $this->getTable('googlebase/types')),
                'items.type_id=types.type_id',
                $this->_getJoinFields('types'));
        
        return $this;
    }
    
    protected function _getJoinFields ($tableAlias) 
    {
        $joinFields = array(
            'items' => array(
                'item_id'           => 'item_id', 
                'gbase_item_id'     => 'gbase_item_id', 
                'item_type_id'      => 'items.type_id', 
                'published'         => 'published', 
                'expires'           => 'expires', 
                'impr'              => 'impr', 
                'clicks'            => 'clicks', 
                'views'             => 'views'
            ),

            'types' => array(
                'gbase_itemtype'    => 'types.gbase_itemtype'
            )
        );
        
        return isset($joinFields[$tableAlias]) ? $joinFields[$tableAlias] : array();
    }
    
    protected function _getAllJoinFeilds () 
    {
        return array_merge(
            array_keys($this->_getJoinFields('items')),
            array_keys($this->_getJoinFields('types'))
        );
    }
}