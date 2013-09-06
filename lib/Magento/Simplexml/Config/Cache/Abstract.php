<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    Magento_Simplexml
 * @copyright  {copyright}
 * @license    {license_link}
 */


/**
 * Abstract class for configuration cache
 *
 * @category   Magento
 * @package    Magento_Simplexml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Magento_Simplexml_Config_Cache_Abstract extends Magento_Object
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
     * @return Magento_Simplexml_Config_Cache_Abstract
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
