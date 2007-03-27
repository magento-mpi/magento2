<?php

class Mage_Sales_Shipping
{

    function getShippingQuotes($data, $type='')
    {
        $types = $this->getType('shipping', $type);
        $quotes = array();
        foreach ($types as $type) {
            $className = (string)$type->class;
            $obj = new $className();
            $quote = $obj->fetchQuotes($data);
            $quotes = array_merge_recursive($quotes, $quote);
        }
        return $quotes;
    }
}