<?php
/**
 * Cms Hierarchy Model for config processing
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\VersionsCms\Model\Hierarchy;

class Config extends \Magento\Config\Data\Scoped
    implements \Magento\VersionsCms\Model\Hierarchy\ConfigInterface
{
    /**
     * Menu layouts configuration
     * @var array
     */
    protected $_contextMenuLayouts = null;

    /**
     * Scope priority loading scheme
     *
     * @var array
     */
    protected $_scopePriorityScheme = array('global');

    /**
     * @param \Magento\VersionsCms\Model\Hierarchy\Config\Reader $reader
     * @param \Magento\Config\ScopeInterface $configScope
     * @param \Magento\Config\CacheInterface $cache
     * @param string $cacheId
     */
    public function __construct(
        \Magento\VersionsCms\Model\Hierarchy\Config\Reader $reader,
        \Magento\Config\ScopeInterface $configScope,
        \Magento\Config\CacheInterface $cache,
        $cacheId = "menuHierarchyConfigCache"
    ) {
        parent::__construct($reader, $configScope, $cache, $cacheId);
    }

    /**
     * Return available Context Menu layouts output
     *
     * @return array
     */
    public function getAllMenuLayouts()
    {
        return $this->get();
    }

    /**
     * Return Context Menu layout by its name
     *
     * @param string $layoutName
     * @return \Magento\Object|boolean
     */
    public function getContextMenuLayout($layoutName)
    {
        $menuLayouts = $this->get();
        return isset($menuLayouts[$layoutName]) ? $menuLayouts[$layoutName] : false;
    }
}
