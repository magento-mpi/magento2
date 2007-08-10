<?php
/**
 * Translate model
 *
 * @package    Mage
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
    protected $_cache = array();
    
    public function __construct() 
    {
        $this->_language = Mage::getSingleton('core/store')->getLanguageCode();
        $this->_sections = (array) Mage::getConfig()->getNode('translate');
		$this->_adapter  = 'csv';
        $this->_translate = new Zend_Translate($this->_adapter, 
        	Mage::getDesign()->getTranslateFilename('base.csv', array(
        		'_language' => $this->getLanguage(),
        	)), $this->getLanguage()
       	);
        
        // TODO: dynamic load
        foreach ($this->_sections as $section=>$sectionFile) {
            if (!empty($section)) {
                $this->loadTranslationFile($sectionFile);
            }
        }
    }
    
    public function loadTranslationFile($file)
    {
        $this->_translate->addTranslation(	
        	Mage::getDesign()->getTranslateFilename($file, array(
    		'_language' => $this->getLanguage(),
    		)), $this->getLanguage()
    	);
    	return $this;
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
        if (empty($this->_language)) {
            return 'en';
        }
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
    	Varien_Profiler::start('translate');
        $text = array_shift($args);
        if (isset($this->_cache[$text])) {
            $translated = $this->_cache[$text];
        } else {
            $translated = $this->_translate->_($text);
            $this->_cache[$text] = $translated;
        }
        array_unshift($args, $translated);
        $result = call_user_func_array('sprintf', $args);
        Varien_Profiler::stop('translate');
        return $result;
    }
}