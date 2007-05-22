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
    public function setCode($code)
    {
        $this->setData('code', $code);
        
        $config = $this->getConfig();
        if ($config) {
            $this->setId((int)$config->id);
            $this->setLanguage((string)$config->language);
            $this->setGroup((string)$config->group);
        }
        
        return $this;
    }
    
    public function getId()
    {
        if ($this->getWebsiteId()) {
            return $this->getWebsiteId();
        }
        return (int) $this->getConfig()->id;
    }
    
    public function getResource()
    {
        return Mage::getSingleton('core_resource', 'website');
    }
    
    public function load($websiteId)
    {
        $this->setData($this->getResource()->load($websiteId));
        return $this;
    }
    
    public function getConfig()
    {
        return Mage::getConfig()->getWebsiteConfig($this->getCode());
    }
    
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
    
    public function getDir($type)
    {
        return $this->getConfig()->filesystem->$type;
    }
    
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
}