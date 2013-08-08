<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_FullPageCache
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_FullPageCache_Model_Config extends Magento_Simplexml_Config
{
    protected $_placeholders = null;

    /**
     * Class constructor
     * load cache configuration
     *
     * @param Magento_Core_Model_Config_Modules_Reader $configReader
     * @param Magento_Core_Model_Cache_Type_Config $configCacheType
     * @param $data
     */
    public function __construct(
        Magento_Core_Model_Config_Modules_Reader $configReader,
        Magento_Core_Model_Cache_Type_Config $configCacheType,
        $data = null
    ) {
        parent::__construct($data);
        $cacheId = 'placeholders_config';
        $cachedXml = $configCacheType->load($cacheId);
        if ($cachedXml) {
            $this->loadString($cachedXml);
        } else {
            $config = $configReader->loadModulesConfiguration('placeholder.xml');
            $xmlConfig = $config->getNode();
            $configCacheType->save($xmlConfig->asNiceXml(), $cacheId);
            $this->setXml($xmlConfig);
        }
    }

    /**
     * Initialize all declared placeholders as array
     * @return Magento_FullPageCache_Model_Config
     */
    protected function _initPlaceholders()
    {
        if ($this->_placeholders === null) {
            $this->_placeholders = array();
            foreach ($this->getNode('placeholders')->children() as $placeholder) {
                $this->_placeholders[(string)$placeholder->block][] = array(
                    'container'     => (string)$placeholder->container,
                    'code'          => (string)$placeholder->placeholder,
                    'cache_lifetime'=> (int) $placeholder->cache_lifetime,
                    'name'          => (string) $placeholder->name
                );
            }
        }
        return $this;
    }

    /**
     * Create placeholder object based on block information
     *
     * @param Magento_Core_Block_Abstract $block
     * @return Magento_FullPageCache_Model_Container_Placeholder
     */
    public function getBlockPlaceholder($block)
    {
        $this->_initPlaceholders();
        $type = $block->getType();

        if (isset($this->_placeholders[$type])) {
            $placeholderData = false;
            foreach ($this->_placeholders[$type] as $placeholderInfo) {
                if (!empty($placeholderInfo['name'])) {
                    if ($placeholderInfo['name'] == $block->getNameInLayout()) {
                        $placeholderData = $placeholderInfo;
                    }
                } else {
                    $placeholderData = $placeholderInfo;
                }
            }

            if (!$placeholderData) {
                return false;
            }

            $placeholder = $placeholderData['code']
                . ' container="' . $placeholderData['container'] . '"'
                . ' block="' . get_class($block) . '"';
            $placeholder.= ' cache_id="' . $block->getCacheKey() . '"';
            foreach ($block->getCacheKeyInfo() as $k => $v) {
                if (is_string($k) && !empty($k)) {
                    $placeholder .= ' ' . $k . '="' . $v . '"';
                }
            }
            $placeholder = Mage::getModel('Magento_FullPageCache_Model_Container_Placeholder',
                array('definition' => $placeholder));
            return $placeholder;
        }
        return false;
    }
}
