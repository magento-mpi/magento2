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
    protected $_language;
    protected $_translate;
    protected $_loadedSections = array();
    protected $_cache = array();
    
    public function __construct() 
    {
        $this->_language = Mage::getSingleton('core/store')->getLanguageCode();
        $this->_translate = new Zend_Translate('array', $this->getTranslationArray(), $this->_language);
    }
    
    public function getResource()
    {
        return Mage::getResourceSingleton('core/translate');
    }
    
    public function getTranslationArray()
    {
        return $this->getResource()->getTranslationArray();
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
        if (isset($this->_cache[$text])) {
            $translated = $this->_cache[$text];
        } else {
            $translated = $this->_translate->_($text);
            $this->_cache[$text] = $translated;
        }
        array_unshift($args, $translated);
        $result = call_user_func_array('sprintf', $args);
        return $result;
    }
}