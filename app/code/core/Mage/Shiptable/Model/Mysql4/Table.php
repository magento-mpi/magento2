<?php

class Mage_Shiptable_Model_Mysql4_Table extends Mage_Shiptable_Model_Mysql4
{
    public function getRate(Mage_Sales_Shipping_Quote_Request $request)
    {
        $shiptableTable = $this->_getTableName('shiptable_setup', 'shiptable');
        $select = $this->_read->select()->from($shiptableName);
        $select->where($this->_read->quoteInto('dest_country=?', $request->destCountry));
        $select->where($this->_read->quoteInto('dest_region=?', $request->destRegion));
        $select->where($this->_read->quoteInto('condition_name=?', $request->conditionName));
        $select->where($this->_read->quoteInto('condition_value>?', $request->{$request->conditionName}));
        $select->order('condition_value')->limit(1);
        $row = $this->_read->fetchAssoc($select);
        return $row;
    }
}