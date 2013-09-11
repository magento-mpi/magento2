<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Api
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Wsdl config model
 *
 * @category   Magento
 * @package    Magento_Api
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Api\Model\Wsdl;

class Config extends \Magento\Api\Model\Wsdl\Config\Base
{
    protected static $_namespacesPrefix = null;

    /**
     * @var \Magento\Core\Model\Config\Modules\Reader
     */
    protected $_configReader;

    /**
     * @var \Magento\Core\Model\Cache\Type\Config
     */
    protected $_configCacheType;

    /**
     * @param \Magento\Core\Model\Config\Modules\Reader $configReader
     * @param \Magento\Core\Model\Cache\Type\Config $configCacheType
     * @param \Magento\Simplexml\Element|null $sourceData
     */
    public function __construct(
        \Magento\Core\Model\Config\Modules\Reader $configReader,
        \Magento\Core\Model\Cache\Type\Config $configCacheType,
        $sourceData = null
    ) {
        parent::__construct($sourceData);
        $this->_configReader = $configReader;
        $this->_configCacheType = $configCacheType;
    }

    /**
     * Return wsdl content
     *
     * @return string
     */
    public function getWsdlContent()
    {
        return $this->_xml->asXML();
    }

    /**
     * Return namespaces with their prefix
     *
     * @return array
     */
    public static function getNamespacesPrefix()
    {
        if (is_null(self::$_namespacesPrefix)) {
            self::$_namespacesPrefix = array();
            $config = \Mage::getSingleton('Magento\Api\Model\Config')->getNode('v2/wsdl/prefix')->children();
            foreach ($config as $prefix => $namespace) {
                self::$_namespacesPrefix[$namespace->asArray()] = $prefix;
            }
        }
        return self::$_namespacesPrefix;
    }

    protected function _loadCache($id)
    {
        return $this->_configCacheType->load($id);
    }

    protected function _saveCache($data, $id, $tags = array(), $lifetime = false)
    {
        return $this->_configCacheType->save($data, $id, $tags, $lifetime);
    }

    protected function _removeCache($id)
    {
        return $this->_configCacheType->remove($id);
    }

    public function init()
    {
        $cachedXml = $this->_configCacheType->load($this->_cacheId);
        if ($cachedXml) {
            $this->loadString($cachedXml);
        } else {
            $mergeWsdl = new \Magento\Api\Model\Wsdl\Config\Base();
            $mergeWsdl->setHandler($this->getHandler());

            /** @var \Magento\Api\Helper\Data $helper */
            $helper = \Mage::helper('Magento\Api\Helper\Data');
            if ($helper->isWsiCompliant()) {
                /**
                 * Exclude Magento_Api wsdl xml file because it used for previous version
                 * of API wsdl declaration
                 */
                $mergeWsdl->addLoadedFile($this->_configReader->getModuleDir('etc', "Magento_Api") . DS . 'wsi.xml');

                $baseWsdlFile = $this->_configReader->getModuleDir('etc', "Magento_Api") . DS . 'wsi.xml';
                $this->loadFile($baseWsdlFile);
                $this->_configReader->loadModulesConfiguration('wsi.xml', $this, $mergeWsdl);
            } else {
                $baseWsdlFile = $this->_configReader->getModuleDir('etc', "Magento_Api") . DS . 'wsdl.xml';
                $this->loadFile($baseWsdlFile);
                $this->_configReader->loadModulesConfiguration('wsdl.xml', $this, $mergeWsdl);
            }

            $this->_configCacheType->save($this->getXmlString(), $this->_cacheId);
        }
        return $this;
    }

    /**
     * Return Xml of node as string
     *
     * @return string
     */
    public function getXmlString()
    {
        return $this->getNode()->asXML();
    }
}
