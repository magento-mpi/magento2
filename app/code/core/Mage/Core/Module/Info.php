<?
class Mage_Core_Module_Info 
{
    protected $_config = null;
    protected $_setupClass = null;
    
    public function __construct(Varien_Xml $config) 
    {
        if (!isset($config->codePool)) {
            $config->addChild('codePool', 'core');
        }

        $this->_config = new Mage_Core_Config_Xml('xml', $config);
        $this->getSetupClass()->applyDbUpdates();
    }
    
    public function getConfig($xpath='')
    {
        if (''===$xpath) {
            return $this->_config->getXml();
        } else {
            return $this->_config->getXpath($xpath);
        }
    }
    
    public function isFront() 
    {
        return !empty($this->getConfig()->controller->front) ? true : false;
    }
    
    public function isActive() 
    {
        return !empty($this->getConfig()->active) ? true : false;
    }
    
    public function getSetupClass()
    {
        if (is_null($this->_setupClass)) {
            $setupClassName = $this->getName().'_Setup';
            $this->_setupClass = new $setupClassName($this);
        }
        return $this->_setupClass;
    }
    
    public function getName()
    {
        $config = $this->getConfig();
        return $config['name'];
    }

    public function getFrontName() 
    {
        if (!empty($this->getConfig()->controller->frontName)) {
            return $this->getConfig()->controller->frontName;
        } else {
            return strtolower($this->getName());
        }
    }
    
    public function getDbVersion()
    {
        return Mage::getModel('core', 'Module')->getDbVersion($this->getName());
    }
    
    public function getCodeVersion()
    {
        return $this->getConfig()->version;
    }
    
    public function getRoot($type='') 
    {
        $dir = Mage::getRoot('code').DS.$this->getConfig()->codePool.DS.str_replace('_', DS, $this->getName());
        switch ($type) {
            case 'etc':
                $dir .= DS.'etc';
                break;
                
            case 'controllers':
                $dir .= DS.'controllers';
                break;
                
            case 'views':
                //$dir .= DS.'views';
                $dir = Mage::getRoot('layout').DS.str_replace('_', DS, $this->getName()).DS.'views';
                break;
                
            case 'sql':
                $dir .= DS.'sql';
                break;
        }
        return $dir;
    }
    
    public function getBaseUrl($type='')
    {
        $url = Mage::getBaseUrl($type);
        switch ($type) {
            case 'skin':
                $url .= '/skins/default';
                break;
                
            default:
                $url .= '/'.$this->getFrontName();
                break;
        }
        return $url;
    }
/*
    public function loadConfig($name)
    {
        if ($name=='*user*') {
            $fileName = Mage::getRoot('etc').DS
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
                Mage::addConfigSection($section, $callback);
            }
        }
        
        $sections = Mage::getConfigSection();
        foreach ($config as $sectionName=>$sectionData) {
            if (isset($sections[$sectionName])) {
                call_user_func($sections[$sectionName], $sectionData, $this);
            }
        }
        
        $this->getSetupClass()->applyDbUpdates();
        
        Mage_Core_Controller::loadModuleConfig($this);
    }
*/
    static public function checkDepends($config)
    {
        foreach ($config as $depModule=>$dummy) {
            if (!Mage::getModuleInfo($depModule)) {
                Mage::exception('Module '.$name.' is missing required dependancy '.$depModule);
            }
        }
    }
}