<?
class Ecom_Core_Module_Info 
{
    protected $_config = null;
    protected $_moduleClass = null;
    
    public function __construct(Zend_Config $config) 
    {
        if (!isset($config->active)) {
            $config->active = false;
        }
        if (!isset($config->mainClass)) {
            $config->mainClass = 'Module';
        }
        if (!isset($config->codePool)) {
            $config->codePool = 'core';
        }

        $this->setConfig($config);
    }
    
    public function setConfig($key, $value='')
    {
        if ($key instanceof Zend_Config) {
            if (is_null($this->_config)) {
                $this->_config = $key;
            } else {
                foreach ($key as $k=>$v) {
                    $this->_config->$k = $v;
                }
            }            
        } elseif (is_string($key)) {
            $this->_config->$key = $value;   
        }
    }
    
    public function getConfig($key='')
    {
        if (''===$key) {
            return $this->_config;
        } else {
            return $this->_config->$key;
        }
    }
    
    public function isFront() 
    {
        return !empty($this->getConfig('controller')->front) ? true : false;
    }
    
    public function isActive() 
    {
        return $this->getConfig('active') ? true : false;
    }

    public function setClass($class)
    {
        $this->_moduleClass = $class;
        return $this->_moduleClass;
    }
    
    public function getClass()
    {
        if (empty($this->_moduleClass)) {
            $this->loadClass();
        }
        return $this->_moduleClass;
    }
    
    public function getName()
    {
        return $this->getConfig('name');
    }
    
    public function getClassName() 
    {
        if ($this->getConfig('mainClass')) {
            return $this->getName().'_'.$this->getConfig('mainClass');
        } else {
            return false;
        }
    }

    public function getFrontName() 
    {
        if (!empty($this->getConfig('controller')->frontName)) {
            return $this->getConfig('controller')->frontName;
        } else {
            return strtolower($this->getName());
        }
    }
    
    public function getDbVersion()
    {
        return Ecom::getModel('core', 'Module')->getDbVersion($this -> getName());
    }
    
    public function getCodeVersion()
    {
        return $this->getClass()->getInfo('version');
    }
    
    public function getRoot($type='') 
    {
        $dir = Ecom::getRoot('code').DS.$this->getConfig('codePool')
            .DS.str_replace('_', DS, $this->getName());
        switch ($type) {
            case 'etc':
                $dir .= DS.'etc';
                break;
                
            case 'controllers':
                $dir .= DS.'controllers';
                break;
                
            case 'views':
                //$dir .= DS.'views';
                $dir = Ecom::getRoot('layout').DS.str_replace('_', DS, $this->getName()).DS.'views';
                break;
                
            case 'sql':
                $dir .= DS.'sql';
                break;
        }
        return $dir;
    }
    
    public function getBaseUrl($type='')
    {
    	$url = '';
    	switch ($type) {
    		default:
    		    $url = Ecom::getBaseUrl($type) . '/' . $this->getFrontName();
    			break;
    	}
    	return $url;
    }
    
    /**
     * Load module main class
     *
     * @return Ecom_Core_Module_Abstract
     */
    public function loadClass()
    {
        if (!$this->isActive()) {
            Ecom::exception('Module '.$this->getName().' is inactive');
        }

        if (!($className = $this->getClassName())) {
            Ecom::exception('Class name not set for module '.$this->getName().'.');
        }

        #Ecom::loadClass($className);

        if (!class_exists(strtolower($className))) {
            Ecom::exception('Class '.$className.' was not loaded');
        }

        $this->setClass(new $className());

        return $this->getClass();
    }
    
    public function loadConfig($name)
    {
        if ($name=='*user*') {
            $fileName = Ecom::getRoot('etc').DS
                .str_replace('_', DS, $this->getName()).'.ini';
        } else {
            $fileName = $this->getRoot('etc').DS
                .str_replace('_', DS, $name).'.ini';
        }
        
        if (!is_readable($fileName)) {
            return false;
        }

        $config = new Zend_Config_Ini($fileName, null, true);

        $this->setConfig($config);
        
        return $this;
    }
    
    public function processConfig()
    {
        $config = $this->getConfig();
        if (isset($config->configSections)) {
            foreach ($config->configSections as $section=>$handler) {
                $callback = explode('::', $handler);
                Ecom::addConfigSection($section, $callback);
            }
        }
        
        $sections = Ecom::getConfigSection();
        foreach ($config as $sectionName=>$sectionData) {
            if (isset($sections[$sectionName])) {
                call_user_func($sections[$sectionName], $sectionData);
            }
        }
        
        Ecom_Core_Controller::loadModuleConfig($this);
    }
    
    static public function checkDepends($config)
    {
        foreach ($config as $depModule=>$dummy) {
            if (!Ecom::getModuleInfo($depModule)) {
                Ecom::exception('Module '.$name.' is missing required dependancy '.$depModule);
            }
        }
    }
}