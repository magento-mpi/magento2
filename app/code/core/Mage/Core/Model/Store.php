<?php
/**
 * Store
 *
 * @package    Mage
 * @subpackage Core
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Core_Model_Store extends Varien_Object
{
    protected $_priceFilter;
    
    protected $_website;
    
    protected $_configCache = array();
    
    public function __construct()
    {
        parent::__construct();
        $this->setIdFieldName($this->getResource()->getIdFieldName());
    }
    
    /**
     * Set store code
     *
     * @param   string $code
     * @return  Mage_Core_Model_Store
     */
    public function setCode($code)
    {
        $this->setData('code', $code);
        $config = $this->getConfig('core/id');
        $this->setId((int)$this->getConfig('core/id'));
        $this->setLanguageCode((string)$this->getConfig('core/language'));
        $this->setWebsiteCode((string)$this->getConfig('core/website'));
        Mage::dispatchEvent('setStoreCode', array('store'=>$this));
        
        return $this;
    }
    
    public function findById($id)
    {
        $stores = Mage::getConfig()->getNode('global/stores');
        foreach ($stores->children() as $code=>$store) {
            if ((int)$store->core->id===$id) {
                $this->setCode($code);
                break;
            }
        }
        return $this;
    }
    
    /**
     * Get store id
     *
     * @return int
     */
    public function getId()
    {
        if ($id = parent::getId()) {
            return $id;
        }
        return (int) $this->getConfig('core/id');
    }
    
    /**
     * Get store resource model
     *
     * @return mixed
     */
    public function getResource()
    {
        return Mage::getResourceSingleton('core/store');
    }
    
    /**
     * Load store data
     *
     * @param   int $storeId
     * @return  Mage_Core_Model_Store
     */
    public function load($storeId)
    {
        $this->setData($this->getResource()->load($storeId));
        return $this;
    }
    
    /**
     * Get store config data
     *
     * @param string $section
     * @return mixed
     */
    public function getConfig($sectionVar='')
    {
        if (isset($this->_configCache[$sectionVar])) {
            return $this->_configCache[$sectionVar];
        }
        
        $sectionArr = explode('/', $sectionVar);
        
        if (empty($sectionArr[0])) {
            $result = Mage::getConfig()->getNode('global/stores/'.$this->getCode());
            if (!$result || $result->is('default')) {
                $result = $this->getWebsite()->getConfig();
            }
        } else {
            $result = Mage::getConfig()->getNode('global/stores/'.$this->getCode().'/'.$sectionArr[0]);
            if (!$result || $result->is('default') || (isset($sectionArr[1]) && !$result->{$sectionArr[1]})) {
                $result = $this->getWebsite()->getConfig($sectionVar);
            } elseif (isset($sectionArr[1])) {
                $result = $result->{$sectionArr[1]};
            }
        }
        
        $this->_configCache[$sectionVar] = $result;
        return $result;
    }
    
    public function getWebsite()
    {
        if (empty($this->_website)) {
            $this->_website = Mage::getModel('core/website')->setCode($this->getWebsiteCode());
        }
        return $this->_website;
    }
    
    /**
     * Get store directory by type
     *
     * @param   string $type
     * @return  string
     */
    public function getDir($type)
    {
        static $cache;
        
        if (isset($cache[$type])) {
            return $cache[$type];
        }
        $dir = (string)$this->getConfig("filesystem/$type");
        if (!$dir) {
            $dir = $this->getDefaultDir($type);
        }
        
        if (!$dir) {
            throw Mage::exception('Mage_Core', 'Invalid base dir type specified: '.$type);
        }
        
        switch ($type) {
            case 'var': case 'session': case 'cache_config': case 'cache_layout': case 'cache_block':
                if (!file_exists($dir)) {
                    mkdir($dir, 0777, true);
                }
                break;
        }
        
        $dir = str_replace('/', DS, $dir);
        
        $cache[$type] = $dir;
        
        return $dir;
    }
    
    public function getDefaultDir($type)
    {
        $dir = Mage::getRoot();
        switch ($type) {
            case 'etc':
                $dir = Mage::getRoot().DS.'etc';
                break;
                
            case 'code':
                $dir = Mage::getRoot().DS.'code';
                break;
                
            case 'var':
                $dir = $this->getTempVarDir();
                break;
                
            case 'session':
                $dir = $this->getDir('var').DS.'session';
                break;
                
            case 'cache_config':
                $dir = $this->getDir('var').DS.'cache'.DS.'config';
                break;
                                    
            case 'cache_layout':
                $dir = $this->getDir('var').DS.'cache'.DS.'layout';
                break;
                
            case 'cache_block':
                $dir = $this->getDir('var').DS.'cache'.DS.'block';
                break;
                
        }
        return $dir;
    }
    
    public function getTempVarDir()
    {
        return (!empty($_ENV['TMP']) ? $_ENV['TMP'] : '/tmp').'/magento/var';
    }
    
    /**
     * Get store url
     *
     * @param   array $params
     * @return  string
     */
    public function getUrl($params)
    {
        static $cache;
        
        $cacheKey = md5(serialize($params));
        if (isset($cache[$cacheKey])) {
            return $cache[$cacheKey];
        }
        
        if (!empty($_SERVER['HTTPS'])) {
            if (!empty($params['_type']) && ('skin'===$params['_type'] || 'js'===$params['_type'])) {
                $params['_secure'] = true;
            }
        }
        
        $section = empty($params['_secure']) ? 'unsecure' : 'secure';
        $config = $this->getConfig($section);
        $protocol = (string)$config->protocol;
        $host = (string)$config->host;
        $port = (int)$config->port;
        $basePath = (string)$config->base_path;
        if (!empty($params['_type'])) {
            $basePath = (string)$this->getConfig('url/'.$params['_type']);
        }
        
        $url = $protocol.'://'.$host;
        $url .= ('http'===$protocol && 80===$port || 'https'===$protocol && 443===$port) ? '' : ':'.$port;
        $url .= empty($basePath) ? '/' : $basePath;
        
        $cache[$cacheKey] = $url;

        return $url;
    }
    
    /**
     * Get default store currency code
     *
     * @return string
     */
    public function getDefaultCurrencyCode()
    {
        $currencyConfig = $this->getConfig('core/currency');
        return (string) $currencyConfig->default;
    }
    
    /**
     * Set current store currency code
     * 
     * @param   string $code
     * @return  string
     */
    public function setCurrentCurrencyCode($code)
    {
        $code = strtoupper($code);
        if (in_array($code, $this->getAvailableCurrencyCodes())) {
            Mage::getSingleton('core/session')->setCurrencyCode($code);
        }
        return $this;
    }

    /**
     * Get current store currency code
     *
     * @return string
     */
    public function getCurrentCurrencyCode()
    {
        $code = Mage::getSingleton('core/session')->getCurrencyCode();
        if (in_array($code, $this->getAvailableCurrencyCodes())) {
            return $code;
        }
        return $this->getDefaultCurrencyCode();
    }

    /**
     * Get allowed store currency codes
     *
     * @return array
     */
    public function getAvailableCurrencyCodes()
    {
        $availableCurrency = $this->getConfig('core/currency')->available;
        if (!empty($availableCurrency)) {
            return array_keys($this->getConfig('core/currency')->available->asArray());
        }
        return array();
    }
    
    /**
     * Convert price from default currency to current currency
     *
     * @param   double $price
     * @return  double
     */
    public function convertPrice($price)
    {
        if ($this->getCurrentCurrency() && $this->getDefaultCurrency()) {
            return $this->getDefaultCurrency()->convert($price, $this->getCurrentCurrency());
        }
        else {
            return $price;
        }
    }
    
    /**
     * Format price with currency filter (taking rate into consideration)
     *
     * @param   double $price
     * @return  string
     */
    public function formatPrice($price)
    {
        return $this->getPriceFilter()->filter($price);
    }
    
    /**
     * Get store price filter
     *
     * @return unknown
     */
    public function getPriceFilter()
    {
        if (!$this->_priceFilter) {
            if ($this->getDefaultCurrency() && $this->getCurrentCurrency()) {
                $this->_priceFilter = $this->getCurrentCurrency()->getFilter();
                $this->_priceFilter->setRate($this->getDefaultCurrency()->getRate($this->getCurrentCurrency()));
            }
            elseif($this->getDefaultCurrency()) {
                $this->_priceFilter = $this->getDefaultCurrency()->getFilter();
            }
            else {
                $this->_priceFilter = new Varien_Filter_Sprintf('%s', 2);
            }
        }
        return $this->_priceFilter;
    }
    
    public function getDatashareStores($feature)
    {
        $config = $this->getConfig('datashare/'.$feature);
        $shared = array();
        if (!empty($config)) {
            foreach ($config->children() as $storeCode=>$isShared) {
                if ($isShared) {
                    $store = Mage::getModel('core/store')->setCode($storeCode);
                    $shared[$storeCode] = $store->getId();
                }
            }
        }
        return $shared;
    }
    
    public function getEmptyCollection()
    {
        return Mage::getResourceModel('core/store_collection');
    }
}
