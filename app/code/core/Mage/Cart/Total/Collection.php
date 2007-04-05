<?php

class Mage_Cart_Total_Collection
{
    protected $_cart = null;
    protected $_totals = array();
    
    public function __construct(Mage_Cart_Cart $cart)
    {
        $this->_cart = $cart;
    }
    
    public function reset()
    {
        $this->_totals = array();
    }
    
    public function append(Mage_Cart_Total_Abstract $total)
    {
        $this->_totals[] = $total;
    }
    
    public function collect()
    {
        $totalsConfig = Mage::getConfig('Mage_Cart')->getTotals();

        foreach ($totalsConfig as $total) {
            $className = (string)$total->class;
            $this->append(new $className($this->_cart));
        }

        return $this;
    }
    
    public function asArray($type='')
    {
        $totals = array();
        foreach ($this->_totals as $total) {
            $totalRows = $total->getTotals();
            foreach ($totalRows as $row) {
                if (''===$type && $type!==$row['type']
                || '_output'===$type && empty($row['output'])) {
                    continue;
                }
                $totals[] = $row;
            }
        }
        return $totals;
    }
}