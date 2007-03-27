<?php

class Mage_Core_Layout_Object extends Varien_Simplexml_Object
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
        
        $class = (string)Mage::getConfig()->getXml()->global->blockTypes->$type->class;
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
        
        $process = (string)$this['process'];
        if (!empty($process)) {
            $moduleName = (string)$args->module;
            extract(Mage::getConfig()->getPathVars($moduleName));
            $args = explode(',', $process);
            foreach ($args as $argName) {
                eval('$this->$argName = "'.addslashes((string)$this->$argName).'";');
            }
        }
        
        return $this;
    }
    
    public function prepareActionArgument($args)
    {
        return $this;
    }

}