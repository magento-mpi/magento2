<?php

/**
 * Abstract class for configuration cache
 *
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @package     Varien
 * @subpackage  Simplexml
 * @author      Moshe Gurvich <moshe@varien.com>
 */
abstract class Varien_Simplexml_Config_Cache_Abstract extends Varien_Object
{
    /**
     * Constructor
     * 
     * Initializes components and allows to save the cache
     *
     * @param array $data
     */
    public function __construct($data=array())
    {
        parent::__construct($data);

        $this->setComponents(array());
        $this->setIsAllowedToSave(true);
    }
    
    /**
     * Add configuration component to stats
     *
     * @param string $component Filename of the configuration component file
     * @return Varien_Simplexml_Config_Cache_Abstract
     */
    public function addComponent($component)
    {
        $comps = $this->getComponents();
        if (is_readable($component)) {
            $comps[$component] = array('mtime'=>filemtime($component));
        }
        $this->setComponents($comps);
        
        return $this;
    }
    
    /**
     * Validate components in the stats
     *
     * @param array $data
     * @return boolean
     */
    public function validateComponents($data)
    {
    	if (empty($data) || !is_array($data)) {
    		return false;
    	}
        // check that no source files were changed or check file exsists
        foreach ($data as $sourceFile=>$stat) {
            if (empty($stat['mtime']) || !is_file($sourceFile) || filemtime($sourceFile)!==$stat['mtime']) {
                return false;
            }
        }
        return true;
    }

    public function getComponentsHash()
    {
        $sum = '';
        foreach ($this->getComponents() as $comp) {
            $sum .= $comp['mtime'].':';
        }
        $hash = md5($sum);
        return $hash;
    }
}
