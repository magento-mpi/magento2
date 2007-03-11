<?php

class Varien_Xml extends SimpleXMLElement 
{
    function extend($source, $overwrite=false)
    {
        $sourceChildren = $source->children();
        
        foreach ($sourceChildren as $child) {
            $this->extendChild($child, $overwrite);
        }
        
        return $this;
    }
    
    function extendChild($source, $overwrite=false)
    {
        // this will be our new target node
        $target = null;
        
        // name of the source node
        $sourceName = $source->getName();
        
        // here we have children of our source node
        $sourceChildren = $source->children();
        
        $exists = $this->$sourceName;
        
        if (!$sourceChildren) {
            // handle string node
            if (isset($exists)) {
                if ($overwrite) {
                    unset($exists);
                } else {
                    return $this;
                }
            }
            $this->addChild($sourceName, (string)$source);
            return $this;
        }
        
        if (isset($exists)) {
            // search for target child with same name subnode as node's name
            foreach ($exists as $existsNode) {
                if ((string)$existsNode->name==(string)$source->name) {
                    $target = $existsNode;
                    break;
                }
            }
        }
        
        if (is_null($target)) {
            // if child target is not found create new and descend
            $target = $this->addChild($sourceName);
        }
        
        // finally add our source node children to resulting new target node
        foreach ($sourceChildren as $childKey=>$childNode) {
            $target->extend($childNode);
        }        
        
        return $this;
    }
    
}