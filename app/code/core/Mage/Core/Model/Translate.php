<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Translate model
 *
 * @category   Mage
 * @package    Mage_Core
 * @author     Dmitriy Soroka <dmitriy@varien.com>
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