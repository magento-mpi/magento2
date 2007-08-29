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
 * @category   default
 * @package    Varien_Simplexml
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Base class for simplexml based configurations
 *
 * @category   default
 * @package    Varien_Simplexml
 * @author      Moshe Gurvich <moshe@varien.com>
 */
class Varien_Simplexml_Config
{
    /**
     * Configuration xml
     *
     * @var Varien_Simplexml_Element
     */
    protected $_xml = null;
    
    protected $_cacheId = null;
    
    protected $_cacheChecksum = false;

    /**
     * Cache resource object
     *
     * @var Varien_Simplexml_Config_Cache_Abstract
     */
    protected $_cache = null;

    /**
     * Class name of simplexml elements for this configuration
     *
     * @var string
     */
    protected $_elementClass = 'Varien_Simplexml_Element';
    
    /**
     * Xpath describing nodes in configuration that need to be extended
     * 
     * @example <allResources extends="/config/modules//resource"/>
     */
    protected $_xpathExtends = "//*[@extends]";

    /**
     * Constructor
     * 
     * Initializes XML for this configuration
     *
     * @see self::setXml
     * @param string|Varien_Simplexml_Element $sourceData
     * @param string $sourceType
     */
    public function __construct($sourceData=null) {
        if (is_null($sourceData)) {
            return;
        }
	    if ($sourceData instanceof Varien_Simplexml_Element) {
	       $this->setXml($sourceData);
	    } elseif (is_string($sourceData) && !empty($sourceData)) {
	        if (strlen($sourceData)<1000 && is_readable($sourceData)) {
	            $this->loadFile($sourceData);
	        } else {
	            $this->loadString($sourceData);
	        }
	    }
        #$this->setCache(new Varien_Simplexml_Config_Cache_File());
        #$this->getCache()->setConfig($this);
    }

    /**
     * Sets xml for this configuration
     * 
     * @param Varien_Simplexml_Element $sourceData
     * @return Varien_Simplexml_Config
     */
    public function setXml(Varien_Simplexml_Element $node)
    {
        $this->_xml = $node;
        return $this;
    }
    
    /**
     * Returns node found by the $path
     *
     * @see     Varien_Simplexml_Element::descend
     * @param   string $path
     * @return  Varien_Simplexml_Element
     */
    public function getNode($path=null)
    {
        if (!$this->_xml instanceof Varien_Simplexml_Element) {
            return false;
        } elseif (empty($path)) {
            return $this->_xml;
        } else {
            return $this->_xml->descend($path);
        }
    }

    /**
     * Returns nodes found by xpath expression
     *
     * @param string $xpath
     * @return array
     */
    public function getXpath($xpath)
    {
        if (empty($this->_xml)) {
            return false;
        }

        if (!$result = @$this->_xml->xpath($xpath)) {
            return false;
        }

        return $result;
    }
    
    public function setCache($cache)
    {
        $this->_cache = $cache;
        return $this;
    }
    
    public function getCache()
    {
        return $this->_cache;
    }
    
    public function setCacheId($id)
    {
        $this->_cacheId = $id;
        return $this;
    }
    
    public function getCacheId()
    {
        return $this->_cacheId;
    }
    
    public function setCacheChecksum($data)
    {
    	if (false===$data || 0===$data) {
    		$this->_cacheChecksum = false;
    	} else {
        	$this->_cacheChecksum = md5($data);
    	}
        return $this;
    }
    
    public function updateCacheChecksum($data)
    {
    	if (false===$this->getCacheChecksum()) {
    		return $this;
    	}
    	if (false===$data || 0===$data) {
    		$this->_cacheChecksum = false;
    	} else {
        	$this->setCacheChecksum($this->getCacheChecksum().':'.$data);
    	}
        return $this;
    }
    
    public function getCacheChecksum()
    {
        return $this->_cacheChecksum;
    }
    
    public function getCacheChecksumId()
    {
        return $this->getCacheId().'__CHECKSUM';
    }
    
    public function validateCacheChecksum()
    {
        $newChecksum = $this->getCacheChecksum();
        if (false===$newChecksum) {
        	return false;
        }
        if (is_null($newChecksum)) {
            return true;
        }
        $cachedChecksum = $this->getCache()->load($this->getCacheChecksumId());
        return $newChecksum===false && $cachedChecksum===false || $newChecksum===$cachedChecksum;
    }
    
    public function loadCache()
    {
        if (!$this->validateCacheChecksum()) {
            return false;
        }
        
        $xmlString = $this->getCache()->load($this->getCacheId());
        $xml = simplexml_load_string($xmlString, $this->_elementClass);
        if ($xml) {
            $this->_xml = $xml;
            return true;
        }
        
        return false;
    }
    
    public function saveCache($tags=array())
    {
    	if ($this->getCacheChecksum()) {
	        $this->getCache()->save($this->getCacheChecksum(), $this->getCacheChecksumId(), $tags);
	        
	        $xmlString = $this->getNode()->asXml();
	        $this->getCache()->save($xmlString, $this->getCacheId(), $tags);
    	}
        return $this;
    }
    
    public function removeCache()
    {
        $this->getCache()->remove($this->getCacheId());
        $this->getCache()->remove($this->getCacheChecksumId());
        return $this;
    }

    /**
     * Imports XML file
     *
     * @param string $filePath
     * @return Varien_Simplexml_Element
     */
    public function loadFile($filePath)
    {
        if (!is_readable($filePath)) {
            throw new Exception('Can not read xml file '.$filePath);
        }

        $fileData = file_get_contents($filePath);
        $fileData = $this->processFileData($fileData);
        return $this->loadString($fileData, $this->_elementClass);
    }

    /**
     * Imports XML string
     *
     * @param string $string
     * @return Varien_Simplexml_Element
     */
    public function loadString($string)
    {
    	if (!empty($string)) {
    		$xml = simplexml_load_string($string, $this->_elementClass);
    	}
    	else {
    		throw new Exception('"$string" parameter for simplexml_load_string is empty');
    	}
        
    	if ($xml instanceof Varien_Simplexml_Element) {
    	    $this->_xml = $xml;
    	    return true;
    	}

        return false;
    }

    /**
     * Imports DOM node
     *
     * @param DOMNode $dom
     * @return Varien_Simplexml_Element
     */
    public function loadDom($dom)
    {
        $xml = simplexml_import_dom($dom, $this->_elementClass);
        
        if ($xml) {
            $this->_xml = $xml;
            return true;
        }

        return false;
    }

    /**
     * Create node by $path and set its value.
     *
     * @param string $path separated by slashes
     * @param string $value
     * @param boolean $overwrite
     * @return Varien_Simplexml_Config
     */
    public function setNode($path, $value, $overwrite=true)
    {
        $arr1 = explode('/', $path);
        $arr = array();
        foreach ($arr1 as $v) {
            if (!empty($v)) $arr[] = $v;
        }
        $last = sizeof($arr)-1;
        $xml = $this->_xml;
        foreach ($arr as $i=>$nodeName) {
            if ($last===$i) {
                if (!isset($xml->$nodeName) || $overwrite) {
                    $xml->$nodeName = $value;
                }
            } else {
                if (!isset($xml->$nodeName)) {
                    $xml = $xml->addChild($nodeName);
                } else {
                    $xml = $xml->$nodeName;
                }
            }

        }
        return $this;
    }

    /**
     * Process configuration xml
     *
     * @return Varien_Simplexml_Config
     */
    public function applyExtends()
    {
        $targets = $this->getXpath($this->_xpathExtends);
        if (!$targets) {
            return $this;
        }

        foreach ($targets as $target) {
            $sources = $this->getXpath((string)$target['extends']);
            if ($sources) {
                foreach ($sources as $source) {
                    $target->extend($source);
                }
            } else {
                #echo "Not found extend: ".(string)$target['extends'];
            }
            #unset($target['extends']);
        }
        return $this;
    }

    /**
     * Stub method for processing file data right after loading the file text
     *
     * @param string $text
     * @return string
     */
    public function processFileData($text)
    {
        return $text;
    }
    
    public function extend(Varien_Simplexml_Config $config, $overwrite=true)
    {
        $this->getNode()->extend($config->getNode(), $overwrite);
        return $this;
    }
}
