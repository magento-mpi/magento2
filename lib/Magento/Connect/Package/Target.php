<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Connect
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Class to get targets and their basepath from target.xml.
 *
 * @category    Magento
 * @package     Magento_Connect
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_Connect_Package_Target
{

    /**
    * Object contains all contents from target.xml.
    *
    * @var array
    */
    protected $_targetMap=null;

    /**
    * Cache for targets.
    *
    * @var array
    */
    protected $_targets;

    /**
    * Retrieve content from target.xml.
    *
    * @return SimpleXMLElement
    */
    protected function _getTargetMap()
    {
        if (is_null($this->_targetMap)) {
            $this->_targetMap = array();
            $this->_targetMap[] = array('name'=>"magecore" ,'label'=>"Magento module file" , 'uri'=>"./app/code");
            $this->_targetMap[] = array('name'=>"magedesign" ,'label'=>"Magento User Interface (layouts, templates)" , 'uri'=>"./app/design");
            $this->_targetMap[] = array('name'=>"mageetc" ,'label'=>"Magento Global Configuration" , 'uri'=>"./app/etc");
            $this->_targetMap[] = array('name'=>"magelib" ,'label'=>"Magento PHP Library file" , 'uri'=>"./lib");
            $this->_targetMap[] = array('name'=>"magelocale" ,'label'=>"Magento Locale language file" , 'uri'=>"./app/locale");
            $this->_targetMap[] = array('name'=>"magemedia" ,'label'=>"Magento Media library" , 'uri'=>"./media");
            $this->_targetMap[] = array('name'=>"mageskin" ,'label'=>"Magento Theme Skin (Images, CSS, JS)" , 'uri'=>"./skin");
            $this->_targetMap[] = array('name'=>"mageweb" ,'label'=>"Magento Other web accessible file" , 'uri'=>".");
            $this->_targetMap[] = array('name'=>"magetest" ,'label'=>"Magento PHPUnit test" , 'uri'=>"./tests");
            $this->_targetMap[] = array('name'=>"mage" ,'label'=>"Magento other" , 'uri'=>".");
        }        
        return $this->_targetMap;
    }

    /**
    * Retrieve targets as associative array from target.xml.
    *
    * @return array
    */
    public function getTargets()
    {
        if (!is_array($this->_targets)) {            
            $this->_targets = array();
            if($this->_getTargetMap()) {           
                foreach ($this->_getTargetMap() as $_target) {
                    $this->_targets[$_target['name']] = (string)$_target['uri'];
                }
            }
        }
        return $this->_targets;
    }

    /**
    * Retrieve tragets with label for select options.
    *
    * @return array
    */
    public function getLabelTargets()
    {
        $targets = array();
        foreach ($this->_getTargetMap() as $_target) {
            $targets[$_target['name']] = $_target['label'];
        }
        return $targets;
    }

    /**
    * Get uri by target's name.
    *
    * @param string $name
    * @return string
    */
    public function getTargetUri($name)
    {
        foreach ($this->getTargets() as $_name=>$_uri) {
            if ($name == $_name) {
                return $_uri;
            }
        }
        return '';
    }


}