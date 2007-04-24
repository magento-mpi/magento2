<?php

class Mage_Core_Model_Layout_Element extends Varien_Simplexml_Element
{    
    public function prepare($args)
    {
        switch ($this->getName()) {
            case 'layoutUpdate':
                break;
                
            case 'block':
                $this->prepareBlock($args);
                break;
                
            case 'reference':
                $this->prepareReference($args);
                break;
                
            case 'action':
                $this->prepareAction($args);
                break;
                
            default:
                $this->prepareActionArgument($args);
                break;
        }
        $children = $this->children();
        foreach ($this as $child) {
            $child->prepare($args);
        }
        return $this;
    }
    
    public function prepareBlock($args)
    {
        $type = (string)$this['type'];
        $name = (string)$this['name'];
        
        $class = Mage::getConfig()->getXml("global/blockTypes/$type")->getClassName();
        $parent = $this->getParent();
        
        $this->addAttribute('class', $class);
        if (isset($parent['name'])) {
            $this->addAttribute('parent', (string)$parent['name']);
        }
        
        return $this;
    }
    
    public function prepareReference($args)
    {
        #$name = (string)$this->getAttribute('name');
        
        return $this;
    }
    
    public function prepareAction($args)
    {
        #$method = (string)$this->getAttribute('method');
        $parent = $this->getParent();
        $this->addAttribute('block', (string)$parent['name']);
        
        return $this;
    }
    
    public function prepareActionArgument($args)
    {
        return $this;
    }

}