<?php
/**
 * Translate model
 *
 * @package    Ecom
 * @subpackage Core
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Core_Model_Translate
{
    protected $_baseDir;
    protected $_language;
    protected $_adapter;
    protected $_translate;
    protected $_sections;
    protected $_loadedSections = array();
    
    public function __construct() 
    {
        $this->_language = 'en';
        $this->_adapter  = 'csv';
        if (Mage::registry('website')->getIsAdmin()) {
            $this->_sections = (array) Mage::getConfig()->getNode('admin/translate');
        }
        else {
            $this->_sections = (array) Mage::getConfig()->getNode('front/translate');
        }
        
        $this->_baseDir = Mage::getBaseDir('translate').DS.$this->_language.DS;
        
        $this->_translate = new Zend_Translate($this->_adapter, $this->_baseDir.'base.csv', $this->_language);
        
        // TODO: dynamic load
        foreach ($this->_sections as $section=>$sectionFile) {
            $this->_translate->addTranslation($this->_baseDir.$sectionFile, $this->_language);
        }
    }
    
    public function setLanguage($language)
    {
        if (!isset($this->_loadedSections[$language])) {
            $this->_loadedSections[$language] = array();
        }
        $this->_language = $language;
        return $this;
    }
    
    public function getLanguage()
    {
        return $this->_language;
    }
    
    /**
     * Translate
     *
     * @param   array $args
     * @return  string
     */
    public function translate($args)
    {
        $text = array_shift($args);
        $text = $this->_translate->_($text);
        array_unshift($args, $text);
        return call_user_func_array('sprintf', $args);
    }
}