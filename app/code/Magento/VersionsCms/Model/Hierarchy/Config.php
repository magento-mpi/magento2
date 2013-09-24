<?php
/**
 * Cms Hierarchy Model for config processing
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_VersionsCms_Model_Hierarchy_Config extends Magento_Config_Data_Scoped
    implements Magento_VersionsCms_Model_Hierarchy_ConfigInterface
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
     * @param Magento_VersionsCms_Model_Hierarchy_Config_Reader $reader
     * @param Magento_Config_ScopeInterface $configScope
     * @param Magento_Config_CacheInterface $cache
     * @param string $cacheId
     */
    public function __construct(
        Magento_VersionsCms_Model_Hierarchy_Config_Reader $reader,
        Magento_Config_ScopeInterface $configScope,
        Magento_Config_CacheInterface $cache,
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
     * @return Magento_Object|boolean
     */
    public function getContextMenuLayout($layoutName)
    {
        $menuLayouts = $this->get();
        return isset($menuLayouts[$layoutName]) ? $menuLayouts[$layoutName] : false;
    }
}
