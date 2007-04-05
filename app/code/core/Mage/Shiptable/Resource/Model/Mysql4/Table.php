<?php

class Mage_Shiptable_Resource_Model_Mysql4_Table extends Mage_Shiptable_Resource_Model_Mysql4
{
    public function getRate(Mage_Sales_Shipping_Quote_Request $request)
    {
        $shiptableTable = $this->_getTableName('shiptable', 'shiptable');
        $select = $this->_read->select()->from($shiptableTable);
        $select->where($this->_read->quoteInto('dest_country_id=?', $request->getDestCountryId()));
        $select->where($this->_read->quoteInto('dest_region_id=?', $request->getDestRegionId()));
        $select->where($this->_read->quoteInto('condition_name=?', $request->getConditionName()));
        $select->where($this->_read->quoteInto('condition_value>?', $request->getData($request->getConditionName())));
        $select->order('condition_value')->limit(1);
        $row = $this->_read->fetchRow($select);
        return $row;
    }
}