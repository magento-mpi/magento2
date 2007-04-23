<?php

class Mage_Sales_Model_Mysql4_Counter
{   
    protected $_write;
    protected $_read;
    protected $_counterTable;
    
    public function __construct()
    {
        $this->_read = Mage::registry('resource')->getConnection('sales', 'read');
        $this->_write = Mage::registry('resource')->getConnection('sales', 'write');
        $this->_counterTable = Mage::registry('resource')->getTableName('sales', 'counter');
    }
    
    public function getCounter($type, $website=null, $increase=true)
    {
        if (is_null($website)) {
            $website = Mage::getSingleton('core', 'website')->getId();
        }

        $condition = $this->_write->quoteInto("counter_type=? and counter_website=?", array($type, $website));
        
        $this->_write->beginTransaction();
        try {
            $value = $this->_write->fetchOne("select counter_value from ".$this->_counterTable." where ".$condition);
            if (!$value) {
                $value = 1;
                $this->_write->insert($this->_counterTable, array("counter_type"=>$type, "counter_website"=>$website, "counter_value"=>$increase ? $value+1 : $value));
            } elseif ($increase) {
                $this->_write->update($this->_counterTable, array("counter_value"=>Zend_Db_Expr("counter_value+1")), $condition);
            }
            $this->_write->commit();
        } catch (Mage_Core_Exception $e) {
            throw $e;
        } catch (Exception $e) {
            self::$_write->rollBack();
        }

        return $value;
    }
}