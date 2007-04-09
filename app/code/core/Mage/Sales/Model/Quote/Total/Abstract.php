<?php

abstract class Mage_Sales_Model_Quote_Total_Abstract
{
    abstract function getTotals(Mage_Sales_Model_Quote $quote);
}