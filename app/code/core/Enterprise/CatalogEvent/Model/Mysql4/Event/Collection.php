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
 * @category   Enterprise
 * @package    Enterprise_CatalogEvent
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Catalog Event resource collection
 *
 * @category   Enterprise
 * @package    Enterprise_CatalogEvent
 */
class Enterprise_CatalogEvent_Model_Mysql4_Event_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{

    protected $_categoryDataAdded = false;

    /**
     * Intialize collection
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('enterprise_catalogevent/event');
    }

    /**
     * Redefining of standart field to filter adding, for aviability of
     * bit operations for display state
     *
     *
     * @param string|array $attribute
     * @param null|string|array $condition
     * @return Enterprise_CatalogEvent_Model_Mysql4_Event_Collection
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field == 'display_state') {
            $field = $this->_getMappedField($field);
            if (is_array($condition) && isset($condition['eq'])) {
                $condition = $condition['eq'];
            }
            $this->_select->where('(' . $field . ' & ' . (int) $condition . ') = ' . (int) $condition);
            return $this;
        }
        parent::addFieldToFilter($field, $condition);
        return $this;
    }

    /**
     * Set sort order
     *
     * @param string $field
     * @param string $direction
     * @param boolean $unshift
     * @return Enterprise_CatalogEvent_Model_Mysql4_Event_Collection
     */
    protected function _setOrder($field, $direction, $unshift = false)
    {
        if ($field == 'category_name' && $this->_categoryDataAdded) {
            $field = 'category_position';
        }
        return parent::_setOrder($field, $direction, $unshift);
    }

    /**
     * Add category data to collection select (name, position)
     *
     * @return Enterprise_CatalogEvent_Model_Mysql4_Event_Collection
     */
    public function addCategoryData()
    {
        if (! $this->_categoryDataAdded) {
            $this->_select->joinLeft(array('category' => $this->getTable('catalog/category')), 'category.entity_id = main_table.category_id', array('category_position' => 'position'))->joinLeft(array('category_name_attribute' => $this->getTable('eav/attribute')), 'category_name_attribute.entity_type_id = category.entity_type_id
                    AND
                category_name_attribute.attribute_code = \'name\'', array())
                ->joinLeft(array('category_varchar' => $this->getTable('catalog/category') . '_varchar'), 'category_varchar.entity_id = category.entity_id
                    AND
                category_varchar.attribute_id = category_name_attribute.attribute_id
                ', array('category_name' => 'value'));
            $this->_map['fields']['category_name'] = 'category_varchar.value';
            $this->_map['fields']['category_position'] = 'category.position';
            $this->_categoryDataAdded = true;
        }
        return $this;
    }
}