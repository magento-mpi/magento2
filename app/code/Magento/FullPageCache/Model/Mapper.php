<?php
/**
 * Placeholder mapper model. Map block instance to placeholders configuration
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_FullPageCache_Model_Placeholder_Mapper
{
    /**
     * @var Magento_FullPageCache_Model_Placeholder_ConfigInterface
     */
    protected $_config;

    /**
     * @var Magento_FullPageCache_Model_Container_PlaceholderFactory
     */
    protected $_factory;

    /**
     * @param Magento_FullPageCache_Model_Container_PlaceholderFactory $factory
     * @param Magento_FullPageCache_Model_Placeholder_ConfigInterface $config
     */
    public function __construct(
        Magento_FullPageCache_Model_Container_PlaceholderFactory $factory,
        Magento_FullPageCache_Model_Placeholder_ConfigInterface $config
    ) {
        $this->_factory = $factory;
        $this->_config = $config;
    }

    /**
     * Map block instance to placeholder configuration and returns new placeholder instance
     *
     * @param Magento_Core_Block_Abstract $block
     * @return Magento_FullPageCache_Model_Container_Placeholder|null
     */
    public function map(Magento_Core_Block_Abstract $block)
    {
        $type = $block->getType();
        $placeholderData = null;
        foreach ($this->_config->getPlaceholders($type) as $placeholderInfo) {
            if (!empty($placeholderInfo['name'])) {
                if ($placeholderInfo['name'] == $block->getNameInLayout()) {
                    $placeholderData = $placeholderInfo;
                }
            } else {
                $placeholderData = $placeholderInfo;
            }
        }

        if (null === $placeholderData) {
            return null;
        }

        $placeholder = $placeholderData['code']
            . ' container="' . $placeholderData['container'] . '"'
            . ' block="' . get_class($block) . '"'
            . ' cache_id="' . $block->getCacheKey() . '"';

        foreach ($block->getCacheKeyInfo() as $key => $value) {
            if (is_string($key) && !empty($key)) {
                $placeholder .= ' ' . $key . '="' . $value . '"';
            }
        }
        return $this->_factory->create($placeholder);
    }
}
