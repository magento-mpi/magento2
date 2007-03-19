<?php

class Varien_Simplexml_Object extends SimpleXMLElement 
{
    protected $_parent = null;
    
    public function setParent($element)
    {
        $this->_parent = $element;
    }
    
    public function getParent()
    {
        if (!empty($this->_parent)) {
            return $this->_parent;
        } else {
            return $this->xpath('..');
        }
    }
    
    function appendChild($sourceNodes)
    {
        foreach ($sourceNodes as $source) {
            $child = $this->addChild($source->getName());
            
            $attributes = $source->attributes();
            foreach ($attributes as $key=>$value) {
                $child->addAttribute($key, $value);
            }
            
            $sourceChildren = $source->children();
            foreach ($sourceChildren as $sourceChild) {
                $child->appendChild($sourceChild);
            }
        }
    }
    
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
        $targetChild = null;
        
        // name of the source node
        $sourceName = $source->getName();
        
        // here we have children of our source node
        $sourceChildren = $source->children();

        if (!$sourceChildren) {
            // handle string node
            if (isset($this->$sourceName)) {
                if ($overwrite) {
                    unset($this->$sourceName);
                } else {
                    return $this;
                }
            }
            $targetChild = $this->addChild($sourceName, (string)$source);
            foreach ($source->attributes() as $key=>$value) {
                $targetChild->addAttribute($key, $value);
            }
            return $this;
        }
        
        if (isset($this->$sourceName)) {
            $targetChild = $this->$sourceName;
        }
        
        if (is_null($targetChild)) {
            // if child target is not found create new and descend
            $targetChild = $this->addChild($sourceName);
            foreach ($source->attributes() as $key=>$value) {
                $targetChild->addAttribute($key, $value);
            }
        }
        
        // finally add our source node children to resulting new target node
        foreach ($sourceChildren as $childKey=>$childNode) {
            $targetChild->extendChild($childNode);
        }        
        
        return $this;
    }
/*
    function extendChildByNode($source, $overwrite=false, $mergeBy='name')
    {
        // this will be our new target node
        $targetChild = null;
        
        // name of the source node
        $sourceName = $source->getName();
        
        // here we have children of our source node
        $sourceChildren = $source->children();

        if (!$sourceChildren) {
            // handle string node
            if (isset($this->$sourceName)) {
                if ($overwrite) {
                    unset($this->$sourceName);
                } else {
                    return $this;
                }
            }
            $targetChild = $this->addChild($sourceName, (string)$source);
            foreach ($source->attributes() as $key=>$value) {
                $targetChild->addAttribute($key, $value);
            }
            return $this;
        }
        
        if (isset($this->$sourceName)) {
            // search for target child with same name subnode as node's name
            if (isset($source->$mergeBy)) {
                foreach ($this->$sourceName as $targetNode) {
                    if (!isset($targetNode->$mergeBy)) {
                        Zend::exception("Can't merge identified node with non identified");
                    }
                    if ((string)$source->$mergeBy==(string)$targetNode->$mergeBy) {
                        $targetChild = $targetNode;
                        break;
                    }
                }
            } else {
                $existsWithId = false;
                foreach ($this->$sourceName as $targetNode) {
                    if (isset($targetNode->$mergeBy)) {
                        Zend::exception("Can't merge identified node with non identified");
                    }
                }
                $targetChild = $this->$sourceName;
            }
        }
        
        if (is_null($targetChild)) {
            // if child target is not found create new and descend
            $targetChild = $this->addChild($sourceName);
            foreach ($source->attributes() as $key=>$value) {
                $targetChild->addAttribute($key, $value);
            }
        }
        
        // finally add our source node children to resulting new target node
        foreach ($sourceChildren as $childKey=>$childNode) {
            $targetChild->extendChildByNode($childNode, $overwrite, $mergeBy);
        }        
        
        return $this;
    }
    
    function extendChildByAttribute($source, $overwrite=false, $mergeBy='name')
    {
        // this will be our new target node
        $targetChild = null;
        
        // name of the source node
        $sourceName = $source->getName();
        
        // here we have children of our source node
        $sourceChildren = $source->children();

        if (!$sourceChildren) {
            // handle string node
            if (isset($this->$sourceName)) {
                if ($overwrite) {
                    unset($this->$sourceName);
                } else {
                    return $this;
                }
            }
            $targetChild = $this->addChild($sourceName, (string)$source);
            foreach ($source->attributes() as $key=>$value) {
                $targetChild->addAttribute($key, $value);
            }
            return $this;
        }
        
        if (isset($this->$sourceName)) {
            // search for target child with same name subnode as node's name
            if (isset($source[$mergeBy])) {
                foreach ($this->$sourceName as $targetNode) {
                    if (!isset($targetNode[$mergeBy])) {
                        Zend::exception("Can't merge identified node with non identified");
                    }
                    if ((string)$source[$mergeBy]==(string)$targetNode[$mergeBy]) {
                        $targetChild = $targetNode;
                        break;
                    }
                }
            } else {
                $existsWithId = false;
                foreach ($this->$sourceName as $targetNode) {
                    if (isset($targetNode[$mergeBy])) {
                        Zend::exception("Can't merge identified node with non identified");
                    }
                }
                $targetChild = $this->$sourceName;
            }
        }
        
        if (is_null($targetChild)) {
            // if child target is not found create new and descend
            $targetChild = $this->addChild($sourceName);
            foreach ($source->attributes() as $key=>$value) {
                $targetChild->addAttribute($key, $value);
            }
        }
        
        // finally add our source node children to resulting new target node
        foreach ($sourceChildren as $childKey=>$childNode) {
            $targetChild->extendChildByAttribute($childNode, $overwrite, $mergeBy);
        }        
        
        return $this;
    }
*/
}