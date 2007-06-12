<?php

class Mage_Sales_Model_Quote_Rule_Action_Stop extends Mage_Core_Model_Rule_Action_Stop
{
    public function updateQuote(Mage_Sales_Model_Quote $quote)
    {
        $this->getRule()->setStopProcessingRules(true);
        return $this;
    }
}