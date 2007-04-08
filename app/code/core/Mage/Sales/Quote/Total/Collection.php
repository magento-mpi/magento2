<?php

class Mage_Sales_Quote_Total_Collection
{
    protected $_quote = null;
    protected $_totals = array();
    
    public function __construct(Mage_Sales_Quote $quote)
    {
        $this->_quote = $quote;
    }
    
    public function reset()
    {
        $this->_totals = array();
    }
    
    public function append(Mage_Sales_Quote_Total_Abstract $total)
    {
        $this->_totals[] = $total;
    }
    
    public function collect()
    {
        $totalsConfig = Mage::getConfig('Mage_Checkout')->getQuoteTotals();

        foreach ($totalsConfig as $total) {
            $className = $total->getClassName();
            $this->append(new $className($this->_quote));
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