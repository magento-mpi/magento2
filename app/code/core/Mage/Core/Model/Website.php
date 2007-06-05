<?php
/**
 * Website
 *
 * @package    Mage
 * @subpackage Core
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Core_Model_Website extends Varien_Object
{
    /**
     * Set website code
     *
     * @param   string $code
     * @return  Mage_Core_Model_Website
     */
    public function setCode($code)
    {
        $this->setData('code', $code);
        
        $config = $this->getConfig();
        if ($config) {
            $this->setId((int)$config->id);
            $this->setLanguage((string)$config->language);
            $this->setGroup((string)$config->group);
        }
        Mage::dispatchEvent('setWebsiteCode', array('website'=>$this));
        return $this;
    }
    
    /**
     * Get website id
     *
     * @return int
     */
    public function getId()
    {
        if ($this->getWebsiteId()) {
            return $this->getWebsiteId();
        }
        return (int) $this->getConfig()->id;
    }
    
    /**
     * Get website resource model
     *
     * @return mixed
     */
    public function getResource()
    {
        return Mage::getSingleton('core_resource', 'website');
    }
    
    /**
     * Load website data
     *
     * @param   int $websiteId
     * @return  Mage_Core_Model_Website
     */
    public function load($websiteId)
    {
        $this->setData($this->getResource()->load($websiteId));
        return $this;
    }
    
    /**
     * Get website config data
     *
     * @return mixed
     */
    public function getConfig()
    {
        return Mage::getConfig()->getWebsiteConfig($this->getCode());
    }
    
    /**
     * Get website categories
     *
     * @return array
     */
    public function getArrCategoriesId()
    {
        $arr = array();
        // TODO: depended from website id
        $nodes = Mage::getModel('catalog_resource','category_tree')
            ->load(2,10) // TODO: from config
            ->getNodes();
        foreach ($nodes as $node) {
            $arr[] = $node->getId();
        }
        
        return $arr;
    }
    
    /**
     * Get website directory by type
     *
     * @param   string $type
     * @return  string
     */
    public function getDir($type)
    {
        return (string)$this->getConfig()->filesystem->$type;
    }
    
    /**
     * Get website url
     *
     * @param   array $params
     * @return  string
     */
    public function getUrl($params)
    {
        if (isset($params['_admin'])) {
            $isAdmin = $params['_admin'];
        } else {
            $isAdmin = $this->getIsAdmin();
        }
        if (!$isAdmin) {
            if (!empty($_SERVER['HTTPS'])) {
                if (!empty($params['_type']) && ('skin'===$params['_type'] || 'js'===$params['_type'])) {
                    $params['_secure'] = true;
                }
            }
            $config = $this->getConfig();
            $urlConfig = empty($params['_secure']) ? $config->unsecure : $config->secure;
    
            $protocol = (string)$urlConfig->protocol;
            $host = (string)$urlConfig->host;
            $port = (int)$urlConfig->port;
            $basePath = (string)$urlConfig->base_path;
            if (!empty($params['_type'])) {
                $basePath = (string)$config->url->$params['_type'];
            }
            
            $url = $protocol.'://'.$host;
            $url .= ('http'===$protocol && 80===$port || 'https'===$protocol && 443===$port) ? '' : ':'.$port;
            $url .= empty($basePath) ? '/' : $basePath;
        } else {
            $url = dirname($_SERVER['SCRIPT_NAME']).'/';
        }
        return $url;
    }
    
    /**
     * Get default website currency code
     *
     * @return string
     */
    public function getDefaultCurrencyCode()
    {
        return (string) Mage::getConfig()->getWebsiteConfig($this->getCode())->currency->default;
    }
    
    /**
     * Set current website currency code
     * 
     * @param   string $code
     * @return  string
     */
    public function setCurrentCurrencyCode($code)
    {
        $code = strtoupper($code);
        if (in_array($code, $this->getAvailableCurrencyCodes())) {
            Mage::getSingleton('core', 'session')->setCurrencyCode($code);
        }
        return $this;
    }

    /**
     * Get current website currency code
     *
     * @return string
     */
    public function getCurrentCurrencyCode()
    {
        $code = Mage::getSingleton('core', 'session')->getCurrencyCode();
        if (in_array($code, $this->getAvailableCurrencyCodes())) {
            return Mage::getSingleton('core', 'session')->getCurrencyCode();
        }
        return $this->getDefaultCurrencyCode();
    }

    /**
     * Get allowed website currency codes
     *
     * @return array
     */
    public function getAvailableCurrencyCodes()
    {
        return array_keys($this->getConfig()->currency->available->asArray());
    }
}
