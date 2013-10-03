<?php
/**
 * Placeholder mapper model. Map block instance to placeholders configuration
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\FullPageCache\Model\Placeholder;

class Mapper
{
    /**
     * @var \Magento\FullPageCache\Model\Placeholder\ConfigInterface
     */
    protected $_config;

    /**
     * @var \Magento\FullPageCache\Model\Container\PlaceholderFactory
     */
    protected $_factory;

    /**
     * @param \Magento\FullPageCache\Model\Container\PlaceholderFactory $factory
     * @param \Magento\FullPageCache\Model\Placeholder\ConfigInterface $config
     */
    public function __construct(
        \Magento\FullPageCache\Model\Container\PlaceholderFactory $factory,
        \Magento\FullPageCache\Model\Placeholder\ConfigInterface $config
    ) {
        $this->_factory = $factory;
        $this->_config = $config;
    }

    /**
     * Map block instance to placeholder configuration and returns new placeholder instance
     *
     * @param \Magento\Core\Block\AbstractBlock $block
     * @return \Magento\FullPageCache\Model\Container\Placeholder|null
     */
    public function map(\Magento\Core\Block\AbstractBlock $block)
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
