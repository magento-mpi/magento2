<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Widget
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Widget Instance Collection
 *
 * @category    Mage
 * @package     Mage_Widget
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Widget_Model_Resource_Widget_Instance_Collection extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Fields map for corellation names & real selected fields
     *
     * @var array
     */
    protected $_map = array('fields' => array('type' => 'instance_type'));


    /**
     * Constructor
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Mage_Widget_Model_Widget_Instance', 'Mage_Widget_Model_Resource_Widget_Instance');
    }

    /**
     * Filter by store ids
     *
     * @param array|integer $storeIds
     * @param boolean $withDefaultStore if TRUE also filter by store id '0'
     * @return Mage_Widget_Model_Resource_Widget_Instance_Collection
     */
    public function addStoreFilter($storeIds = array(), $withDefaultStore = true)
    {
        if (!is_array($storeIds)) {
            $storeIds = array($storeIds);
        }
        if ($withDefaultStore && !in_array('0', $storeIds)) {
            array_unshift($storeIds, 0);
        }
        $where = array();
        foreach ($storeIds as $storeId) {
            $where[] = $this->_getConditionSql('store_ids', array('finset' => $storeId));
        }

        $this->_select->where(implode(' OR ', $where));

        return $this;
    }
}
