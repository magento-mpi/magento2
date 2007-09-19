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
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Core_Model_Translate
{
    const CSV_SEPARATOR     = ',';
    const SCOPE_SEPARATOR   = '::';
    
    const CONFIG_KEY_AREA   = 'area';
    const CONFIG_KEY_LOCALE = 'locale';
    const CONFIG_KEY_STORE  = 'store';
    
    /**
     * Locale name
     *
     * @var string
     */
    protected $_locale;
    
    /**
     * Translation object
     *
     * @var Zend_Translate_Adapter
     */
    protected $_translate;
    
    /**
     * Translator configuration array
     *
     * @var array
     */
    protected $_config;
    
    /**
     * Cache object
     *
     * @var Zend_Cache_Frontend_File
     */
    protected $_cache;
    
    /**
     * Cache identifier
     *
     * @var string
     */
    protected $_cacheId;
    
    /**
     * Cache checksum
     *
     * @var string
     */
    protected $_cacheChecksum;
    
    /**
     * Checksum cache identifier
     *
     * @var string
     */
    protected $_cacheChecksumId;
    
    /**
     * Translation data
     *
     * @var array
     */
    protected $_data;
    
    /**
     * Translation data for data scope (per module)
     *
     * @var array
     */
    protected $_dataScope;
    
    public function __construct() 
    {
        
    }
    
    /**
     * Initialization translation data
     *
     * @param   string $area
     * @return  Mage_Core_Model_Translate
     */
    public function init($area)
    {
        $this->setConfig(array(self::CONFIG_KEY_AREA=>$area));
        if (!$this->_data = $this->_loadCache()) {
            $this->_data = array();
            
            foreach ($this->getModulesConfig() as $moduleName=>$info) {
                $info = $info->asArray();
                $this->_loadModuleTranslation($moduleName, $info['files']);
            }
            
            $this->_loadThemeTranslation();
            $this->_loadDbTranslation();
            $this->_saveCache();
        }
        
        return $this;
    }
    
    /**
     * Retrieve modules configuration by translation
     *
     * @return Mage_Core_Model_Config_Element
     */
    public function getModulesConfig()
    {
        $config = Mage::getConfig()->getNode($this->getConfig(self::CONFIG_KEY_AREA).'/translate/modules')->children();
        if (!$config) {
            return array();
        }
        return $config;
    }
    
    /**
     * Initialize configuration
     *
     * @param   array $config
     * @return  Mage_Core_Model_Translate
     */
    public function setConfig($config)
    {
        $this->_config = $config;
        if (!isset($this->_config[self::CONFIG_KEY_LOCALE])) {
            $this->_config[self::CONFIG_KEY_LOCALE] = $this->getLocale();
        }
        if (!isset($this->_config[self::CONFIG_KEY_STORE])) {
            $this->_config[self::CONFIG_KEY_STORE] = Mage::getSingleton('core/store')->getId();
        }
        return $this;
    }
    
    /**
     * Retrieve config value by key
     *
     * @param   string $key
     * @return  mixed
     */
    public function getConfig($key)
    {
        if (isset($this->_config[$key])) {
            return $this->_config[$key];
        }
        return null;
    }
    
    /**
     * Loading data from module translation files
     *
     * @param   string $moduleName
     * @param   string $files
     * @return  Mage_Core_Model_Translate
     */
    protected function _loadModuleTranslation($moduleName, $files)
    {
        foreach ($files as $file) {
            $file = $this->_getModuleFilePath($moduleName, $file);
            $this->_addData($this->_getFileData($file), $moduleName);
        }
        return $this;
    }
    
    /**
     * Adding translation data
     *
     * @param array $data
     * @param string $scope
     * @return Mage_Core_Model_Translate
     */
    protected function _addData($data, $scope)
    {
        foreach ($data as $key => $value) {
            $key    = $this->_prepareDataString($key);
            $value  = $this->_prepareDataString($value);
        	if ($scope && isset($this->_dataScope[$key])) {
        	    /**
        	     * Checking previos value
        	     */
        	    $scopeKey = $this->_dataScope[$key] . self::SCOPE_SEPARATOR . $key;
        	    if (!isset($this->_data[$scopeKey])) {
        	        if (isset($this->_data[$key])) {
        	            $this->_data[$scopeKey] = $this->_data[$key];
        	            unset($this->_data[$key]);
        	        }
        	    }
    	        $scopeKey = $scope . self::SCOPE_SEPARATOR . $key;
    	        $this->_data[$scopeKey] = $value;
        	}
        	else {
        	    $this->_data[$key]     = $value;
        	    $this->_dataScope[$key]= $scope;
        	}
        }
        return $this;
    }
    
    protected function _prepareDataString($string)
    {
        return str_replace('""', '"', trim($string, '"'));
    }
    
    /**
     * Loading current theme translation
     *
     * @return Mage_Core_Model_Translate
     */
    protected function _loadThemeTranslation()
    {
        //$design = Mage::getDesign();
        return $this;
    }
    
    /**
     * Loading current store translation from DB
     *
     * @return Mage_Core_Model_Translate
     */
    protected function _loadDbTranslation()
    {
        $this->_addData($this->getResource()->getTranslationArray(), $this->getConfig(self::CONFIG_KEY_STORE));
        return $this;
    }
    
    /**
     * Retrieve translation file for module
     *
     * @param   string $module
     * @return  string
     */
    protected function _getModuleFilePath($module, $fileName)
    {
        $file = Mage::getConfig()->getModuleDir('locale', $module);
        $file.= DS.$this->getLocale().DS.$fileName;
        return $file;
    }
    
    /**
     * Retrieve data from file
     *
     * @param   string $file
     * @return  array
     */
    protected function _getFileData($file)
    {
        $data = array();
        if (file_exists($file)) {
            $translator = new Zend_Translate('csv', $file, $this->getLocale(), array('separator'=>self::CSV_SEPARATOR));
            $data = $translator->getMessages();
        }
        return $data;
    }
    
    /**
     * Retrieve translation data
     *
     * @return array
     */
    public function getData()
    {
        if (is_null($this->_data)) {
            Mage::throwException('You need init translate area');
        }
        return $this->_data;
    }
    
    /**
     * Retrieve locale
     *
     * @return string
     */
    public function getLocale()
    {
        if (is_null($this->_locale)) {
            $this->_locale = Mage::getSingleton('core/locale')->getLocaleCode();
        }
        return $this->_locale;
    }
    
    
    public function getResource()
    {
        return Mage::getResourceSingleton('core/translate');
    }
    
    /**
     * Retrieve translation object
     *
     * @return Zend_Translate_Adapter
     */
    public function getTranslate()
    {
        if (is_null($this->_translate)) {
            $this->_translate = new Zend_Translate('array', $this->getData(), $this->getLocale());
        }
        return $this->_translate;
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
        
        if ($text instanceof Mage_Core_Model_Translate_Expr) {
            $code = $text->getCode(self::SCOPE_SEPARATOR);
            $text = $text->getText();
            if (array_key_exists($code, $this->getData())) {
                $translated = $this->_data[$code];
            }
            elseif (array_key_exists($text, $this->getData())) {
            	$translated = $this->_data[$text];
            }
            else {
                $translated = $text;
            }
        }
        else {
            if (array_key_exists($text, $this->getData())) {
            	$translated = $this->_data[$text];
            }
            else {
                $translated = $text;
            }
        }
        
        //$translated = $this->getTranslate()->_($text);
        array_unshift($args, $translated);
        $result = call_user_func_array('sprintf', $args);
        return $result;
    }
    
    /**
     * Retrieve cache identifier
     *
     * @return string
     */
    public function getCacheId()
    {
        if (is_null($this->_cacheId)) {
            $this->_cacheId = 'translate';
            if (isset($this->_config[self::CONFIG_KEY_LOCALE])) {
                $this->_cacheId.= '_'.$this->_config[self::CONFIG_KEY_LOCALE];
            }
            if (isset($this->_config[self::CONFIG_KEY_AREA])) {
                $this->_cacheId.= '_'.$this->_config[self::CONFIG_KEY_AREA];
            }
            if (isset($this->_config[self::CONFIG_KEY_STORE])) {
                $this->_cacheId.= '_'.$this->_config[self::CONFIG_KEY_STORE];
            }
        }
        return $this->_cacheId;
    }
    
    /**
     * Retrieve cache checksum identifier
     *
     * @return string
     */
    public function getCacheChecksumId()
    {
        return $this->getCacheId().'_CHECKSUM';
    }
    
    /**
     * Retrieve cache checksum
     *
     * @return string
     */
    public function getCacheChecksum()
    {
        if (is_null($this->_cacheChecksum)) {
            /**
             * Collect module files checksum
             */
            foreach ($this->getModulesConfig() as $moduleName=>$info) {
            	$info = $info->asArray();
            	foreach ($info['files'] as $file) {
            		$file = $this->_getModuleFilePath($moduleName, $file);
            		if (file_exists($file)) {
            		    $this->_cacheChecksum.= $moduleName . filemtime($file);
            		}
            	}
            }
            $this->_cacheChecksum.= '_DB_'.$this->getResource()->getMainChecksum();
            $this->_cacheChecksum = md5($this->_cacheChecksum);
        }
        return $this->_cacheChecksum;
    }
    
    /**
     * Retrieve checksum validation status
     *
     * @return bool
     */
    public function isCacheChecksumValid()
    {
        $old    = $this->getCache()->load($this->getCacheChecksumId());
        $current= $this->getCacheChecksum();
        if ($old && $old === $current) {
            return true;
        }
        return false;
    }
    
    /**
     * Retrieve cache object
     *
     * @return Zend_Cache_Frontend_File
     */
    public function getCache()
    {
        if (!$this->_cache) {
            $this->_cache = Zend_Cache::factory('Core', 'File', 
                array('automatic_serialization'=>true), 
                array('cache_dir'=>Mage::getBaseDir('cache_translate'))
            );
        }
        return $this->_cache;
    }
    
    /**
     * Loading data cache
     *
     * @param   string $area
     * @return  array | false
     */
    protected function _loadCache()
    {
        if (!$this->isCacheChecksumValid()) {
            return false;
        }
        return $this->getCache()->load($this->getCacheId());
    }
    
    /**
     * Saving data cache
     *
     * @param   string $area
     * @return  Mage_Core_Model_Translate
     */
    protected function _saveCache()
    {
        $this->getCache()->save($this->getCacheChecksum(), $this->getCacheChecksumId());
        $this->getCache()->save($this->getData(), $this->getCacheId());
        return $this;
    }
}