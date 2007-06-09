<?php

class Mage_Sales_Model_Mysql4_Counter
{   
    protected $_write;
    protected $_read;
    protected $_counterTable;
    
    public function __construct()
    {
        $this->_read = Mage::registry('resources')->getConnection('sales_read');
        $this->_write = Mage::registry('resources')->getConnection('sales_write');
        $this->_counterTable = Mage::registry('resources')->getTableName('sales_resource', 'counter');
    }
    
    public function getCounter($type, $website=null, $increase=true)
    {
        if (is_null($website)) {
            $website = Mage::getSingleton('core/website')->getId();
        }

        $condition = $this->_write->quoteInto("counter_type=?", $type)." and "
            .$this->_write->quoteInto("website_id=?", $website);

        $this->_write->beginTransaction();
        try {
            $value = $this->_write->fetchOne("select counter_value from ".$this->_counterTable." where ".$condition);
            if (!$value) {
                $value = 1;
                $this->_write->insert($this->_counterTable, array("counter_type"=>$type, "website_id"=>$website, "counter_value"=>$increase ? $value+1 : $value));
            } elseif ($increase) {
                $this->_write->update($this->_counterTable, array("counter_value"=>new Zend_Db_Expr("counter_value+1")), $condition);
            }
            $this->_write->commit();
            #$value = str_pad($value, 8, '0', STR_PAD_LEFT);

        } catch (Mage_Core_Exception $e) {
            throw $e;
        } catch (Exception $e) {
            $this->_write->rollBack();
            $value = false;
        }

        return $value;
    }
}