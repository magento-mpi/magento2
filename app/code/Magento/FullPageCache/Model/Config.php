<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_FullPageCache
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\FullPageCache\Model;

class Config extends \Magento\Simplexml\Config
{
    protected $_placeholders = null;

    /**
     * Class constructor
     * load cache configuration
     *
     * @param \Magento\Core\Model\Config\Modules\Reader $configReader
     * @param \Magento\Core\Model\Cache\Type\Config $configCacheType
     * @param $data
     */
    public function __construct(
        \Magento\Core\Model\Config\Modules\Reader $configReader,
        \Magento\Core\Model\Cache\Type\Config $configCacheType,
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
     * @return \Magento\FullPageCache\Model\Config
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
     * @param \Magento\Core\Block\AbstractBlock $block
     * @return \Magento\FullPageCache\Model\Container\Placeholder
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
            $placeholder = \Mage::getModel('\Magento\FullPageCache\Model\Container\Placeholder',
                array('definition' => $placeholder));
            return $placeholder;
        }
        return false;
    }
}
