<?php
/**
 * Cms Hierarchy Model for config processing
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\VersionsCms\Model\Hierarchy;

use Magento\Framework\Config\Data\Scoped;

class Config extends Scoped implements ConfigInterface
{
    /**
     * Menu layouts configuration
     * @var array
     */
    protected $_contextMenuLayouts = null;

    /**
     * Scope priority loading scheme
     *
     * @var string[]
     */
    protected $_scopePriorityScheme = ['global'];

    /**
     * @param \Magento\VersionsCms\Model\Hierarchy\Config\Reader $reader
     * @param \Magento\Framework\Config\ScopeInterface $configScope
     * @param \Magento\Framework\Config\CacheInterface $cache
     * @param string $cacheId
     */
    public function __construct(
        \Magento\VersionsCms\Model\Hierarchy\Config\Reader $reader,
        \Magento\Framework\Config\ScopeInterface $configScope,
        \Magento\Framework\Config\CacheInterface $cache,
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
     * @return \Magento\Framework\Object|bool
     */
    public function getContextMenuLayout($layoutName)
    {
        $menuLayouts = $this->get();
        return isset($menuLayouts[$layoutName]) ? $menuLayouts[$layoutName] : false;
    }
}
