<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Mage_Core_Model_Layout_Update
{
    /**
     * Additional tag for cleaning layout cache convenience
     */
    const LAYOUT_GENERAL_CACHE_TAG = 'LAYOUT_GENERAL_CACHE_TAG';

    /**
     * Layout Update Simplexml Element Class Name
     *
     * @var string
     */
    protected $_elementClass;

    /**
     * @var SimpleXMLElement
     */
    protected $_packageLayout;

    /**
     * Cache key
     *
     * @var string
     */
    protected $_cacheId;

    /**
     * Cache prefix
     *
     * @var string
     */
    protected $_cachePrefix;

    /**
     * Cumulative array of update XML strings
     *
     * @var array
     */
    protected $_updates = array();

    /**
     * Handles used in this update
     *
     * @var array
     */
    protected $_handles = array();

    /**
     * Page handle names sorted by from parent to child
     *
     * @var array
     */
    protected $_pageHandles = array();

    /**
     * Substitution values in structure array('from'=>array(), 'to'=>array())
     *
     * @var array
     */
    protected $_subst = array();

    /**
     * Class constructor
     */
    public function __construct()
    {
        $subst = Mage::getConfig()->getPathVars();
        foreach ($subst as $k=>$v) {
            $this->_subst['from'][] = '{{'.$k.'}}';
            $this->_subst['to'][] = $v;
        }
    }

    /**
     * XML element class name getter
     *
     * @return string
     */
    public function getElementClass()
    {
        if (!$this->_elementClass) {
            $this->_elementClass = Mage::getConfig()->getModelClassName('Mage_Core_Model_Layout_Element');
        }
        return $this->_elementClass;
    }

    /**
     * Reset all registered updates
     *
     * @return Mage_Core_Model_Layout_Update
     */
    public function resetUpdates()
    {
        $this->_updates = array();
        return $this;
    }

    /**
     * Add update
     *
     * @param string $update
     * @return Mage_Core_Model_Layout_Update
     */
    public function addUpdate($update)
    {
        $this->_updates[] = $update;
        return $this;
    }

    /**
     * Get all registered updates as array
     *
     * @return array
     */
    public function asArray()
    {
        return $this->_updates;
    }

    /**
     * Get all registered updates as string
     *
     * @return string
     */
    public function asString()
    {
        return implode('', $this->_updates);
    }

    /**
     * Reset all registered layout handles
     *
     * @return Mage_Core_Model_Layout_Update
     */
    public function resetHandles()
    {
        $this->_handles = array();
        return $this;
    }

    /**
     * Add handle(s) to update
     *
     * @param array|string $handle
     * @return Mage_Core_Model_Layout_Update
     */
    public function addHandle($handle)
    {
        if (is_array($handle)) {
            foreach ($handle as $h) {
                $this->_handles[$h] = 1;
            }
        } else {
            $this->_handles[$handle] = 1;
        }
        return $this;
    }

    public function removeHandle($handle)
    {
        unset($this->_handles[$handle]);
        return $this;
    }

    public function getHandles()
    {
        return array_keys($this->_handles);
    }

    /**
     * Add the first existing (declared in layout updates) page handle along with all parents to the update.
     * Return whether any page handles have been added or not.
     *
     * @param array $handlesToTry
     * @return bool
     */
    public function addPageHandles(array $handlesToTry)
    {
        foreach ($handlesToTry as $pageHandle) {
            $handleWithParents = $this->getPageLayoutHandles($pageHandle);
            if ($handleWithParents) {
                /* replace existing page handles with the new ones */
                foreach ($this->_pageHandles as $pageHandle) {
                    $this->removeHandle($pageHandle);
                }
                $this->_pageHandles = $handleWithParents;
                $this->addHandle($handleWithParents);
                return true;
            }
        }
        return false;
    }

    /**
     * Retrieve page handle names sorted from parent to child
     *
     * @return array
     */
    public function getPageHandles()
    {
        return $this->_pageHandles;
    }

    /**
     * Retrieve the page layout handle along with all parent handles ordered from parent to child
     *
     * @param string $pageHandle
     * @return array
     */
    public function getPageLayoutHandles($pageHandle)
    {
        $result = array();
        while ($this->pageTypeExists($pageHandle)) {
            array_unshift($result, $pageHandle);
            $pageHandle = $this->getPageTypeParent($pageHandle);
        }
        return $result;
    }

    /**
     * Retrieve recursively all children of a page type
     *
     * @param string $parentName
     * @return array
     */
    protected function _getPageTypeChildren($parentName)
    {
        $result = array();
        $xpath = '/layouts/*[@type="page" and ' . ($parentName ? "@parent='$parentName'" : 'not(@parent)') . ']';
        $pageTypeNodes = $this->getPackageLayout()->xpath($xpath) ?: array();
        /** @var $pageTypeNode Varien_Simplexml_Element */
        foreach ($pageTypeNodes as $pageTypeNode) {
            $pageTypeName = $pageTypeNode->getName();
            $result[$pageTypeName] = array(
                'name'     => $pageTypeName,
                'label'    => (string)$pageTypeNode->label,
                'children' => $this->_getPageTypeChildren($pageTypeName),
            );
        }
        return $result;
    }

    /**
     * @param string $pageTypeName
     * @return Varien_Simplexml_Element
     */
    protected function _getPageTypeNode($pageTypeName)
    {
        /* quick validation for non-existing page types */
        if (!$pageTypeName || !isset($this->getPackageLayout()->$pageTypeName)) {
            return null;
        }
        $nodes = $this->getPackageLayout()->xpath("/layouts/{$pageTypeName}[@type='page'][1]");
        return $nodes ? reset($nodes) : null;
    }

    /**
     * Retrieve all page types in the system represented as a hierarchy
     *
     * Result format:
     * array(
     *     'page_type_1' => array(
     *         'name'     => 'page_type_1',
     *         'label'    => 'Page Type 1',
     *         'children' => array(
     *             'page_type_2' => array(
     *                 'name'     => 'page_type_2',
     *                 'label'    => 'Page Type 2',
     *                 'children' => array(
     *                     // ...
     *                 )
     *             ),
     *             // ...
     *         )
     *     ),
     *     // ...
     * )
     *
     * @return array
     */
    public function getPageTypesHierarchy()
    {
        return $this->_getPageTypeChildren('');
    }

    /**
     * Whether a page type is declared in the system or not
     *
     * @param string $pageType
     * @return bool
     */
    public function pageTypeExists($pageType)
    {
        return (bool)$this->_getPageTypeNode($pageType);
    }

    /**
     * Retrieve the label for a page type
     *
     * @param string $pageType
     * @return string|bool
     */
    public function getPageTypeLabel($pageType)
    {
        $pageTypeNode = $this->_getPageTypeNode($pageType);
        return $pageTypeNode ? (string)$pageTypeNode->label : false;
    }

    /**
     * Retrieve the name of the parent for a page type
     *
     * @param string $pageType
     * @return string|null|false
     */
    public function getPageTypeParent($pageType)
    {
        $pageTypeNode = $this->_getPageTypeNode($pageType);
        return $pageTypeNode ? $pageTypeNode->getAttribute('parent') : false;
    }

    /**
     * Get cache id
     *
     * @return string
     */
    public function getCacheId()
    {
        if (!$this->_cacheId) {
            $this->_cacheId = 'LAYOUT_'.Mage::app()->getStore()->getId().md5(join('__', $this->getHandles()));
        }
        return $this->_cacheId;
    }

    /**
     * Set cache id
     *
     * @param string $cacheId
     * @return Mage_Core_Model_Layout_Update
     */
    public function setCacheId($cacheId)
    {
        $this->_cacheId = $cacheId;
        return $this;
    }

    public function loadCache()
    {
        if (!Mage::app()->useCache('layout')) {
            return false;
        }

        if (!$result = Mage::app()->loadCache($this->getCacheId())) {
            return false;
        }

        $this->addUpdate($result);

        return true;
    }

    public function saveCache()
    {
        if (!Mage::app()->useCache('layout')) {
            return false;
        }
        $str = $this->asString();
        $tags = $this->getHandles();
        $tags[] = self::LAYOUT_GENERAL_CACHE_TAG;
        return Mage::app()->saveCache($str, $this->getCacheId(), $tags, null);
    }

    /**
     * Load layout updates by handles
     *
     * @param array|string $handles
     * @return Mage_Core_Model_Layout_Update
     * @throws Magento_Exception
     */
    public function load($handles=array())
    {
        if (is_string($handles)) {
            $handles = array($handles);
        } elseif (!is_array($handles)) {
            throw new Magento_Exception('Invalid layout update handle');
        }

        foreach ($handles as $handle) {
            $this->addHandle($handle);
        }

        if ($this->loadCache()) {
            return $this;
        }

        foreach ($this->getHandles() as $handle) {
            $this->merge($handle);
        }

        $this->saveCache();
        return $this;
    }

    public function asSimplexml()
    {
        $updates = trim($this->asString());
        $updates = '<'.'?xml version="1.0"?'.'><layout>'.$updates.'</layout>';
        return simplexml_load_string($updates, $this->getElementClass());
    }

    /**
     * Merge layout update by handle
     *
     * @param string $handle
     * @return Mage_Core_Model_Layout_Update
     */
    public function merge($handle)
    {
        $this->fetchPackageLayoutUpdates($handle);
        if (Mage::isInstalled()) {
            $this->fetchDbLayoutUpdates($handle);
        }
        return $this;
    }

    /**
     * Get all layout updates for active design theme
     *
     * @return Mage_Core_Model_Layout_Element
     */
    public function getPackageLayout()
    {
        if ($this->_packageLayout) {
            return $this->_packageLayout;
        }
        $storeId = Mage::app()->getStore()->getId();
        $design = Mage::getSingleton('Mage_Core_Model_Design_Package');
        $cacheKey = 'LAYOUT_' . $design->getArea() . '_STORE' . $storeId . '_' . $design->getPackageName() . '_'
            . $design->getTheme('layout');

        $cacheTags = array(self::LAYOUT_GENERAL_CACHE_TAG);
        if (Mage::app()->useCache('layout') && ($layoutStr = Mage::app()->loadCache($cacheKey))) {
            $this->_packageLayout = simplexml_load_string($layoutStr, $this->getElementClass());
        } else {
            $this->_packageLayout = $this->getFileLayoutUpdatesXml(
                $design->getArea(),
                $design->getPackageName(),
                $design->getTheme('layout'),
                $storeId
            );
            if (Mage::app()->useCache('layout')) {
                Mage::app()->saveCache($this->_packageLayout->asXml(), $cacheKey, $cacheTags, null);
            }
        }

        return $this->_packageLayout;
    }

    public function fetchPackageLayoutUpdates($handle)
    {
        $_profilerKey = 'layout_package_update:' . $handle;
        Magento_Profiler::start($_profilerKey);
        $layout = $this->getPackageLayout();
        foreach ($layout->$handle as $updateXml) {
            $this->fetchRecursiveUpdates($updateXml);
            $this->addUpdate($updateXml->innerXml());
        }
        Magento_Profiler::stop($_profilerKey);

        return true;
    }

    public function fetchDbLayoutUpdates($handle)
    {
        $_profilerKey = 'layout_db_update: '.$handle;
        Magento_Profiler::start($_profilerKey);
        $updateStr = $this->_getUpdateString($handle);
        if (!$updateStr) {
            return false;
        }
        Magento_Profiler::stop($_profilerKey);
        return (bool)$updateStr;
    }

    /**
     * Get update string
     *
     * @param string $handle
     * @return mixed
     */
    protected function _getUpdateString($handle)
    {
        return Mage::getResourceModel('Mage_Core_Model_Resource_Layout')->fetchUpdatesByHandle($handle);
    }

    public function fetchRecursiveUpdates($updateXml)
    {
        foreach ($updateXml->children() as $child) {
            if (strtolower($child->getName())=='update' && isset($child['handle'])) {
                $this->merge((string)$child['handle']);
                // Adding merged layout handle to the list of applied hanles
                $this->addHandle((string)$child['handle']);
            }
        }
        return $this;
    }

    /**
     * Collect and merge layout updates from file
     *
     * @param string $area
     * @param string $package
     * @param string $theme
     * @param integer|null $storeId
     * @return Mage_Core_Model_Layout_Element
     * @throws Magento_Exception
     */
    public function getFileLayoutUpdatesXml($area, $package, $theme, $storeId = null)
    {
        if (null === $storeId) {
            $storeId = Mage::app()->getStore()->getId();
        }
        /* @var $design Mage_Core_Model_Design_Package */
        $design = Mage::getSingleton('Mage_Core_Model_Design_Package');

        $layoutParams = array(
            '_area'    => $area,
            '_package' => $package,
            '_theme'   => $theme,
        );

        /*
         * Allow to modify declared layout updates.
         * For example, the module can remove all its updates to not participate in rendering depending on settings.
         */
        $updatesRoot = Mage::app()->getConfig()->getNode($area . '/layout/updates');
        Mage::dispatchEvent('core_layout_update_updates_get_after', array('updates' => $updatesRoot));

        /* Layout update files declared in configuration */
        $updateFiles = array();
        foreach ($updatesRoot->children() as $updateNode) {
            if ($updateNode->file) {
                $module = $updateNode->getAttribute('module');
                if (!$module) {
                    $updateNodePath = $area . '/layout/updates/' . $updateNode->getName();
                    throw new Magento_Exception(
                        "Layout update instruction '{$updateNodePath}' must specify the module."
                    );
                }
                if ($module && Mage::getStoreConfigFlag('advanced/modules_disable_output/' . $module, $storeId)) {
                    continue;
                }
                /* Resolve layout update filename with fallback to the module */
                $filename = $design->getLayoutFilename(
                    (string)$updateNode->file,
                    $layoutParams + array('_module' => $module)
                );
                if (!is_readable($filename)) {
                    throw new Magento_Exception("Layout update file '{$filename}' doesn't exist or isn't readable.");
                }
                $updateFiles[] = $filename;
            }
        }

        /* Custom local layout updates file for the current theme */
        $filename = $design->getLayoutFilename('local.xml', $layoutParams);
        if (is_readable($filename)) {
            $updateFiles[] = $filename;
        }

        $layoutStr = '';
        foreach ($updateFiles as $filename) {
            $fileStr = file_get_contents($filename);
            $fileStr = str_replace($this->_subst['from'], $this->_subst['to'], $fileStr);
            $fileXml = simplexml_load_string($fileStr, $this->getElementClass());
            if (!$fileXml instanceof SimpleXMLElement) {
                continue;
            }
            $layoutStr .= $fileXml->innerXml();
        }
        $layoutStr = '<layouts>' . $layoutStr . '</layouts>';

        $layoutXml = simplexml_load_string($layoutStr, $this->getElementClass());
        return $layoutXml;
    }
}
