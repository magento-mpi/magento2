<?php

class Mage_Sales_Model_Quote_Rule_Action_Collection extends Mage_Core_Model_Rule_Action_Collection
{    
    public function updateQuote(Mage_Sales_Model_Quote $quote)
    {
        foreach ($this->getActions() as $action) {
            $action->updateQuote($quote);
        }
        return $this;
    }
}