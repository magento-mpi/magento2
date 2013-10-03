<?php
/**
 * Placeholder configuration model
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\FullPageCache\Model\Placeholder;

class Config
    extends \Magento\Config\Data\Scoped
    implements \Magento\FullPageCache\Model\Placeholder\ConfigInterface
{
    /**
     * Scope priority loading scheme
     *
     * @var array
     */
    protected $_scopePriorityScheme = array('global');

    /**
     * @param \Magento\FullPageCache\Model\Placeholder\Config\Reader $reader
     * @param \Magento\Config\ScopeInterface $configScope
     * @param \Magento\Config\CacheInterface $cache
     * @param string $cacheId
     */
    public function __construct(
        \Magento\FullPageCache\Model\Placeholder\Config\Reader $reader,
        \Magento\Config\ScopeInterface $configScope,
        \Magento\Config\CacheInterface $cache,
        $cacheId = 'placeholders_config'
    ) {
        parent::__construct($reader, $configScope, $cache, $cacheId);
    }

    /**
     * Get placeholders config by block instance name
     *
     * @param string $name
     * @return array
     */
    public function getPlaceholders($name)
    {
        return $this->get($name, array());
    }
}
