<?php

class Mage_Core_Layout_Object extends Varien_Simplexml_Object
{
    public function prepare($args)
    {
        switch (strtolower($this->getName())) {
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
    }
    
    public function prepareBlock($args)
    {
        $type = (string)$this->getAttribute('type');
        $name = (string)$this->getAttribute('name');
        
        $class = Mage::getConfig('/')->global->blockTypes->$type;
        
        $this->addAttribute('class', $class);
        $this->addAttribute('parent', $this->getParent()->getName());
        
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
        $this->addattribute('block', $this->getParent()->getName());
        
        return $this;
    }
    
    public function prepareActionArgument($args)
    {
        $process = (string)$this->getAttribute('process');
        
        if ('true'===$process) {
            extract(Mage::getConfig()->getPathVars($args));
            $name = $this->getName();
            eval('$processedValue = "'.addslashes((string)$this).'";');
            $this->getParent()->$name = $processedValue;
        }
        
        return $this;
    }

}