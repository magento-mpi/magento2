<?php

class Varien_Db_Adapter_Pdo_Mysql extends Zend_Db_Adapter_Pdo_Mysql 
{
    protected $_transactionLevel=0;
    
    public function beginTransaction()
    {
        if ($this->_transactionLevel===0) {
            parent::beginTransaction();
        }
        $this->_transactionLevel++;
        return $this;
    }
    
    public function commit()
    {
        $this->_transactionLevel--;
        if ($this->_transactionLevel===0) {
            parent::commit();
        }
        return $this;
    }
    
    public function rollback()
    {
        $this->_transactionLevel--;
        if ($this->_transactionLevel===0) {
            return parent::rollback();
        }
        return $this;
    }
}