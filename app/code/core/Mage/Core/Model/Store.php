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
    /**
     * Set store code
     *
     * @param   string $code
     * @return  Mage_Core_Model_Store
     */
    public function setCode($code)
    {
        $this->setData('code', $code);
        
        $config = $this->getConfig();
        if ($config) {
            $this->setId((int)$config->id);
            $this->setLanguage((string)$config->language);
            $this->setGroup((string)$config->group);
            Mage::dispatchEvent('setStoreCode', array('store'=>$this));
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
        if ($this->getStoreId()) {
            return $this->getStoreId();
        }
        return (int) $this->getConfig()->id;
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
     * @return mixed
     */
    public function getConfig()
    {
        return Mage::getConfig()->getStoreConfig($this->getCode());
    }
    
    /**
     * Get store categories
     *
     * @return array
     */
    public function getArrCategoriesId()
    {
        $arr = array();
        // TODO: dependent from store id
        $nodes = Mage::getResourceModel('catalog/category_tree')
            ->load(2,10) // TODO: from config
            ->getNodes();
        foreach ($nodes as $node) {
            $arr[] = $node->getId();
        }
        
        return $arr;
    }
    
    /**
     * Get store directory by type
     *
     * @param   string $type
     * @return  string
     */
    public function getDir($type)
    {
        if ($this->getConfig()) {
            return (string)$this->getConfig()->filesystem->$type;
        }
    }
    
    /**
     * Get store url
     *
     * @param   array $params
     * @return  string
     */
    public function getUrl($params)
    {
        $config = $this->getConfig();
        if ($config) {
            if (!empty($_SERVER['HTTPS'])) {
                if (!empty($params['_type']) && ('skin'===$params['_type'] || 'js'===$params['_type'])) {
                    $params['_secure'] = true;
                }
            }
            
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
            $url = dirname($_SERVER['PHP_SELF']);
        }
        return $url;
    }
    
    /**
     * Get default store currency code
     *
     * @return string
     */
    public function getDefaultCurrencyCode()
    {
        if (Mage::getConfig()->getStoreConfig($this->getCode())) {
            return (string) Mage::getConfig()->getStoreConfig($this->getCode())->currency->default;
        }
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
        if ($this->getConfig()) {
            return array_keys($this->getConfig()->currency->available->asArray());
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
}
