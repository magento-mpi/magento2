<?php

class Mage_Core_Model_Design_Package
{
	protected $_area;
	
	protected $_name;
	
	protected $_theme;
	
	protected $_rootDir;
	
	protected $_config = null;
	
	public function __construct()
	{
		
	}
	
	public function getConfig($path=null)
	{
		if (is_null($this->_config)) {
			$filename = $this->getEtcFilename('config.xml');
			$config = Mage::getModel('core/config_base');
			$config->loadFile($filename);
			
			if (empty($config)) {
				$filename = $this->getEtcFilename('config.xml', array('_theme'=>$this->getDefaultTheme()));
				$config = Mage::getModel('core/config_base');
				$config->loadFile($filename);	
			}
			
			if (empty($config)) {
				$this->_config = false;
			} else {
				$this->_config = $config;
			}
		}
		if ($this->_config===false) {
			return false;
		} else {
			return (string)$this->_config->getNode($path);
		}
	}
	
	public function setArea($area)
	{
		$this->_area = $area;
		
		return $this;
	}
	
	public function getArea()
	{
		if (empty($this->_area)) {
			$this->_area = $tihs->getDefaultArea();
		}
		return $this->_area;
	}
	
	public function setPackageName($name)
	{
		$this->_name = $name;
		
		return $this;
	}
	
	public function getPackageName()
	{
		if (empty($this->_name)) {
			$this->_name = $this->getDefaultPackage();
		}
		return $this->_name;
	}
	
	public function setTheme($theme)
	{
		$this->_theme = $theme;
		return $this;
	}
	
	public function getTheme()
	{
		if (empty($this->_theme)) {
			$this->_theme = $this->getDefaultTheme();
		}
		return $this->_theme;
	}
	
	public function getDefaultArea()
	{
		return 'frontend';
	}
	
	public function getDefaultPackage()
	{
		return 'default';
	}
	
	public function getDefaultTheme()
	{
		return 'default';
	}
	
	public function updateParamDefaults(array &$params)
	{
		if (empty($params['_area'])) {
			$params['_area'] = $this->getArea();
		}
		if (empty($params['_package'])) {
			$params['_package'] = $this->getPackageName();
		}
		if (empty($params['_theme'])) {
			$params['_theme'] = $this->getTheme();
		}
		return $this;
	}
	
	public function getBaseDir(array $params)
	{
		$this->updateParamDefaults($params);
		$baseDir = (empty($params['_relative']) ? Mage::getBaseDir('design').DS : '').
			$params['_area'].DS.$params['_package'].DS.$params['_type'].DS.$params['_theme'];
		return $baseDir;
	}

	public function getTranslateBaseDir(array $params)
	{
		$this->updateParamDefaults($params);
		if (empty($params['_language'])) {
			$params['_language'] = Mage::getStoreConfig('general/local/language');
		}
		$baseDir = (empty($params['_relative']) ? Mage::getBaseDir('design').DS : '').
			$params['_area'].DS.$params['_package'].DS.'translate'.DS.$params['_language'];
		return $baseDir;
	}
	
	public function getSkinBaseDir(array $params=array())
	{
		$this->updateParamDefaults($params);
		$baseDir = (empty($params['_relative']) ? Mage::getBaseDir('design').DS : '').
			$params['_area'].DS.$params['_package'].DS.$params['_theme'];
		return $baseDir;
	}
	
	public function getSkinBaseUrl(array $params=array())
	{
		$this->updateParamDefaults($params);
		$baseUrl = Mage::getBaseUrl($params).'/'
			.$params['_area'].'/'.$params['_package'].'/'.$params['_theme'].'/';
		return $baseUrl;
	}
	
	/**
     * Get absolute file path for requested file or false if doesn't exist
     *
     * Possible params:
     * - _type: 
     * 	 - layout
     *   - template
     *   - skin
     *   - translate
     * - _package: design package, if not set = default
     * - _theme: if not set = default
     * - _file: path relative to theme root
     * 
     * @see Mage_Core_Model_Config::getBaseDir
     * @param string $file
     * @param array $params
     * @return string|boolean
     * 
     */
    public function validateFile($file, array $params)
    {
    	switch ($params['_type']) {
    		case 'skin':
    			$fileName = $this->getSkinBaseDir($params);
    			break;
    			
    		case 'translate':
    			$fileName = $this->getTranslateBasedir($params);
    			break;
    			
    		default:
    			$fileName = $this->getBaseDir($params);
    			break;
    	}
    	$fileName.= DS.$file;

		$testFile = (empty($params['_relative']) ? '' : Mage::getBaseDir('design').DS) . $fileName;
    	if ($this->getDefaultTheme()!==$params['_theme'] && !file_exists($testFile)) {
    		return false;
    	}
    	return $fileName;
    }
    
    /**
     * Use this one to get existing file name with fallback to default
     *
     * $params['_type'] is required
     * 
     * @param string $file
     * @param array $params
     * @return string
     */
    public function getFilename($file, array $params)
    {
    	Varien_Profiler::start(__METHOD__);
    	$this->updateParamDefaults($params);
    	if (empty($params['_default'])) {
    		$params['_default'] = false;
    	}
		$filename = $this->validateFile($file, $params);
		if (false===$filename) {
			if ($this->getDefaultTheme()===$params['_theme']) {
				return $params['_default'];
			}
			$params['_theme'] = $this->getDefaultTheme();
			$filename = $this->validateFile($file, $params);
			if (false===$filename) {
				return $params['_default'];
			}
		}
		Varien_Profiler::stop(__METHOD__);
		return $filename;
    }
    
    public function getEtcFilename($file, array $params=array())
    {   
        $params['_type'] = 'etc'; 
    	return $this->getFilename($file, $params); 
    }
    
    public function getLayoutFilename($file, array $params=array())
    {
    	$params['_type'] = 'layout';
    	return $this->getFilename($file, $params);
    }
    
    public function getTemplateFilename($file, array $params=array())
    {
    	$params['_type'] = 'template';
    	return $this->getFilename($file, $params);
    }
    
    public function getTranslateFilename($file, array $params=array())
    {
    	$params['_type'] = 'translate';
    	return $this->getFilename($file, $params);
    }
    
    /**
     * Get skin file url
     *
     * @param string $file
     * @param array $params
     * @return string
     */
    public function getSkinUrl($file=null, array $params=array())
    {
    	Varien_Profiler::start(__METHOD__);
    	$this->updateParamDefaults($params);
    	if (empty($params['_type'])) {
    		$params['_type'] = 'skin';
    	}
    	if (empty($params['_default'])) {
    		$params['_default'] = false;
    	}
    	if (!empty($file)) {
			$filename = $this->validateFile($file, $params);
			if (false===$filename) {
				if ($this->getDefaultTheme()===$params['_theme']) {
					return $params['_default'];
				}
				$params['_theme'] = $this->getDefaultTheme();
				$filename = $this->validateFile($file, $params);
				if (false===$filename) {
					return $params['_default'];
				}
			}
    	}
		
    	$url = $this->getSkinBaseUrl($params).(!empty($file) ? $file : '');
    	Varien_Profiler::stop(__METHOD__);
    	return $url;
    }
}
