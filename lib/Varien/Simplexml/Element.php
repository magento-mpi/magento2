<?php

class Varien_Simplexml_Element extends SimpleXMLElement 
{
    protected $_parent = null;
    
    public function setParent($element)
    {
        #$this->_parent = $element;
    }
    
    public function getParent()
    {
        if (!empty($this->_parent)) {
            $parent = $this->_parent;
        } else {
            $arr = $this->xpath('..');
            $parent = $arr[0];
        }
        return $parent;
    }

    public function asArray()
    {
    	$r = array();
    	
    	$attributes = $this->attributes();
    	foreach($attributes as $k=>$v) {
    		if ($v) $r['@'][$k] = (string) $v;
    	}

    	if (!($children = $this->children())) {
    		$r['_'] = (string) $this;
    		return $r;
    	}

    	foreach($children as $childName=>$child) {
    		foreach ($child as $index=>$element) {
    			$r[$childName][$index] = $element->asArray();
    		}
    	}
    	
    	return $r;
	}
	
	public function asNiceXml($level=0)
	{
	    $pad = str_pad('', $level*3, ' ', STR_PAD_LEFT);
	    $out = $pad."<".$this->getName();
	    
	    if ($attributes = $this->attributes()) {
	        foreach ($attributes as $key=>$value) {
	            $out .= ' '.$key.'="'.str_replace('"', '\"', (string)$value).'"';
	        }
	    }
	    
	    if ($children = $this->children()) {
	        $out .= ">\n";
	        foreach ($children as $child) {
	            $out .= $child->asNiceXml($level+1);
	        }
	        $out .= $pad."</".$this->getName().">\n";    
	    } else {
	        $value = (string)$this;
	        if (empty($value)) {
	            $out .= " />\n";
	        } else {
	            $out .= ">".$this->xmlentities($value)."</".$this->getName().">\n";
	        }
	    }
	    
	    return $out;
	}
	
	public function xmlentities()
	{
	    return str_replace(array('&', '"', "'", '<', '>'), array('&amp;', '&quot;', '&apos;', '&lt;', '&gt;'), (string)$this);
	}
    
    function appendChild($source)
    {
        if ($source->children()) {
            $child = $this->addChild($source->getName());
        } else {
            $child = $this->addChild($source->getName(), (string)$source);
        }
        $child->setParent($this);
        
        $attributes = $source->attributes();
        foreach ($attributes as $key=>$value) {
            $child->addAttribute($key, (string)$value);
        }
        
        foreach ($source as $sourceChild) {
            $child->appendChild($sourceChild);
        }
    }
    
    function extend($source, $overwrite=false)
    {
        if (!$source instanceof Varien_Simplexml_Element) {
            return $this;
        }
        
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
            $targetChild->setParent($this);
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
            $targetChild->setParent($this);
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