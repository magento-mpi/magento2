<?php

class Mage_Sales_Config
{
    function getType($name, $type='')
    {
        $config = Mage::getConfig()->getXml()->global->$name;
        if (''===$type) {
            $arr = array();
            foreach ($config as $node) {
                if ($node->active) {
                    $arr[$node->getName()] = $node;
                }
            }
            return $arr;
        }
        if (!isset($config->$type)) {
            return false;
        }
        return $config->$type;
    }
    
    function getClass($name, $type)
    {
        $x = $this->getType($name, $type);
        if (!$x) {
            return false;
        }
        $className = (string)$x->class;
        return new $className();
    }

}