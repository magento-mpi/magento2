<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Theme data helper
 */
class Magento_Core_Helper_Theme extends Magento_Core_Helper_Abstract
{
    /**
     * Directories
     *
     * @var Magento_Core_Model_Dir
     */
    protected $_dirs;

    /**
     * Layout merge factory
     *
     * @var Magento_Core_Model_Layout_MergeFactory
     */
    protected $_layoutMergeFactory;

    /**
     * Theme collection model
     *
     * @var Magento_Core_Model_Resource_Theme_Collection
     */
    protected $_themeCollection;

    /**
     * @var Magento_Core_Model_View_FileSystem
     */
    protected $_viewFileSystem;

    /**
     * @param Magento_Core_Helper_Context $context
     * @param Magento_Core_Model_Dir $dirs
     * @param Magento_Core_Model_Layout_MergeFactory $layoutMergeFactory
     * @param Magento_Core_Model_Resource_Theme_Collection $themeCollection
     * @param Magento_Core_Model_View_FileSystem $viewFileSystem
     */
    public function __construct(
        Magento_Core_Helper_Context $context,
        Magento_Core_Model_Dir $dirs,
        Magento_Core_Model_Layout_MergeFactory $layoutMergeFactory,
        Magento_Core_Model_Resource_Theme_Collection $themeCollection,
        Magento_Core_Model_View_FileSystem $viewFileSystem
    ) {
        $this->_dirs = $dirs;
        $this->_layoutMergeFactory = $layoutMergeFactory;
        $this->_themeCollection = $themeCollection;
        $this->_viewFileSystem = $viewFileSystem;
        parent::__construct($context);
    }

    /**
     * Get CSS files of a given theme
     *
     * Returned array has a structure
     * array(
     *   'Magento_Catalog::widgets.css' => 'http://mage2.com/pub/static/frontend/_theme15/en_US/Magento_Cms/widgets.css'
     * )
     *
     * @param Magento_Core_Model_Theme $theme
     * @return array
     */
    public function getCssFiles($theme)
    {
        /** @var $layoutMerge Magento_Core_Model_Layout_Merge */
        $layoutMerge = $this->_layoutMergeFactory->create(array('theme' => $theme));
        $layoutElement = $layoutMerge->getFileLayoutUpdatesXml();

        /**
         * XPath selector to get CSS files from layout added for HEAD block directly
         */
        $xpathSelectorBlocks = '//block[@class="Magento_Page_Block_Html_Head"]'
            . '/block[@class="Magento_Page_Block_Html_Head_Css"]/arguments/argument[@name="file"]';

        /**
         * XPath selector to get CSS files from layout added for HEAD block using reference
         */
        $xpathSelectorRefs = '//reference[@name="head"]'
            . '/block[@class="Magento_Page_Block_Html_Head_Css"]/arguments/argument[@name="file"]';

        $elements = array_merge(
            $layoutElement->xpath($xpathSelectorBlocks) ?: array(),
            $layoutElement->xpath($xpathSelectorRefs) ?: array()
        );

        $params = array(
            'area'       => $theme->getArea(),
            'themeModel' => $theme,
            'skipProxy'  => true
        );

        $basePath = $this->_dirs->getDir(Magento_Core_Model_Dir::ROOT);
        $files = array();
        foreach ($elements as $fileId) {
            $fileId = (string)$fileId;
            $path = $this->_viewFileSystem->getViewFile($fileId, $params);
            $file = array(
                'id'       => $fileId,
                'path'     => Magento_Filesystem::fixSeparator($path),
             );
            $file['safePath'] = $this->getSafePath($file['path'], $basePath);

            //keys are used also to remove duplicates
            $files[$fileId] = $file;
        }

        return $files;
    }

    /**
     * Get CSS files by group
     *
     * @param Magento_Core_Model_Theme $theme
     * @return array
     * @throws LogicException
     */
    public function getGroupedCssFiles($theme)
    {
        $jsDir = Magento_Filesystem::fixSeparator($this->_dirs->getDir(Magento_Core_Model_Dir::PUB_LIB));
        $codeDir = Magento_Filesystem::fixSeparator($this->_dirs->getDir(Magento_Core_Model_Dir::MODULES));
        $designDir = Magento_Filesystem::fixSeparator($this->_dirs->getDir(Magento_Core_Model_Dir::THEMES));

        $groups = array();
        $themes = array();
        foreach ($this->getCssFiles($theme) as $file) {
            $this->_detectTheme($file, $designDir);
            $this->_detectGroup($file, $designDir, $jsDir, $codeDir);

            if (isset($file['theme']) && $file['theme']->getThemeId()) {
                $themes[$file['theme']->getThemeId()] = $file['theme'];
            }

            if (!isset($file['group'])) {
                throw new LogicException(__('Group is missed for file "%1"', $file['safePath']));
            }
            $group = $file['group'];
            unset($file['theme']);
            unset($file['group']);

            if (!isset($groups[$group])) {
                $groups[$group] = array();
            }
            $groups[$group][] = $file;
        }

        if (count($themes) > 1) {
            $themes = $this->_sortThemesByHierarchy($themes);
        }

        $order = array_merge(array($codeDir, $jsDir), array_map(function ($fileTheme) {
            /** @var $fileTheme Magento_Core_Model_Theme */
            return $fileTheme->getThemeId();
        }, $themes));
        $groups = $this->_sortArrayByArray($groups, $order);

        $labels = $this->_getGroupLabels($themes, $jsDir, $codeDir);
        foreach ($groups as $key => $group) {
            usort($group, array($this, '_sortGroupFilesCallback'));
            $groups[$labels[$key]] = $group;
            unset($groups[$key]);
        }
        return $groups;
    }

    /**
     * Detect theme view file belongs to and set it to file data under "theme" key
     *
     * @param array $file
     * @param string $designDir
     * @return Magento_Core_Helper_Theme
     * @throws LogicException
     */
    protected function _detectTheme(&$file, $designDir)
    {
        //TODO use cache here, so files of the same theme share one model

        $isInsideDesignDir = substr($file['path'], 0, strlen($designDir)) == $designDir;
        if (!$isInsideDesignDir) {
            return $this;
        }

        $relativePath = substr($file['path'], strlen($designDir));

        $area = strtok($relativePath, Magento_Filesystem::DIRECTORY_SEPARATOR);
        $theme = strtok(Magento_Filesystem::DIRECTORY_SEPARATOR);

        if ($area === false || $theme === false) {
            throw new LogicException(__('Theme path "%1/%2" is incorrect', $area, $theme));
        }
        $themeModel = $this->_themeCollection->getThemeByFullPath(
            $area . Magento_Core_Model_Theme::PATH_SEPARATOR . $theme
        );

        if (!$themeModel || !$themeModel->getThemeId()) {
            throw new LogicException(
                __('Invalid theme loaded by theme path "%1/%2"', $area, $theme)
            );
        }

        $file['theme'] = $themeModel;

        return $this;
    }

    /**
     * Detect group where file should be placed and set it to file data under "group" key
     *
     * @param array $file
     * @param string $designDir
     * @param string $jsDir
     * @param string $codeDir
     * @return Magento_Core_Helper_Theme
     * @throws LogicException
     */
    protected function _detectGroup(&$file, $designDir, $jsDir, $codeDir)
    {
        $group = null;
        if (substr($file['path'], 0, strlen($designDir)) == $designDir) {
            if (!isset($file['theme']) || !$file['theme']->getThemeId()) {
                throw new LogicException(__('Theme is missed for file "%1"', $file['safePath']));
            }
            $group = $file['theme']->getThemeId();
        } elseif (substr($file['path'], 0, strlen($jsDir)) == $jsDir) {
            $group = $jsDir;
        } elseif (substr($file['path'], 0, strlen($codeDir)) == $codeDir) {
            $group = $codeDir;
        } else {
            throw new LogicException(__('Invalid view file directory "%1"', $file['safePath']));
        }
        $file['group'] = $group;

        return $this;
    }

    /**
     * Sort themes according to their hierarchy
     *
     * @param array $themes
     * @return array
     */
    protected function _sortThemesByHierarchy(array $themes)
    {
        uasort($themes, array($this, '_sortThemesByHierarchyCallback'));
        return $themes;
    }

    /**
     * Sort one associative array according to another array
     *
     * $groups = array(
     *     b => item2,
     *     a => item1,
     *     c => item3,
     * );
     * $order = array(a,b,c);
     * result: array(
     *     a => item1,
     *     b => item2,
     *     c => item3,
     * )
     *
     * @param array $groups
     * @param array $order
     * @return array
     */
    protected function _sortArrayByArray(array $groups, array $order)
    {
        $ordered = array();
        foreach ($order as $key) {
            if (array_key_exists($key, $groups)) {
                $ordered[$key] = $groups[$key];
                unset($groups[$key]);
            }
        }
        return $ordered + $groups;
    }

    /**
     * Get group labels
     *
     * @param array $themes
     * @param string $jsDir
     * @param string $codeDir
     * @return array
     */
    protected function _getGroupLabels(array $themes, $jsDir, $codeDir)
    {
        $labels = array(
            $jsDir => (string)__('Library files'),
            $codeDir => (string)__('Framework files')
        );
        foreach ($themes as $theme) {
            /** @var $theme Magento_Core_Model_Theme */
            $labels[$theme->getThemeId()] = (string)__('"%1" Theme files', $theme->getThemeTitle());
        }
        return $labels;
    }

    /**
     * Callback for sorting files inside group
     *
     * Return "1" if $firstFile should go before $secondFile, otherwise return "-1"
     *
     * @param array $firstFile
     * @param array $secondFile
     * @return int
     */
    protected function _sortGroupFilesCallback(array $firstFile, array $secondFile)
    {
        $hasContextFirst = strpos($firstFile['id'], '::') !== false;
        $hasContextSecond = strpos($secondFile['id'], '::') !== false;

        if ($hasContextFirst && $hasContextSecond) {
            $result = strcmp($firstFile['id'], $secondFile['id']);
        } elseif (!$hasContextFirst && !$hasContextSecond) {
            $result = strcmp($firstFile['id'], $secondFile['id']);
        } elseif ($hasContextFirst) {
            //case when first item has module context and second item doesn't
            $result = 1;
        } else {
            //case when second item has module context and first item doesn't
            $result = -1;
        }
        return $result;
    }

    /**
     * Sort themes by hierarchy callback
     *
     * @param Magento_Core_Model_Theme $firstTheme
     * @param Magento_Core_Model_Theme $secondTheme
     * @return int
     */
    protected function _sortThemesByHierarchyCallback($firstTheme, $secondTheme)
    {
        $parentTheme = $firstTheme->getParentTheme();
        while ($parentTheme) {
            if ($parentTheme->getThemeId() == $secondTheme->getThemeId()) {
                return -1;
            }
            $parentTheme = $parentTheme->getParentTheme();
        }
        return 1;
    }

    /**
     * Get relative file path cut to be safe for public sharing
     *
     * Path is considered from the base Magento directory
     *
     * @param string $filePath
     * @param string $basePath
     * @return string
     */
    public function getSafePath($filePath, $basePath)
    {
        return ltrim(str_ireplace($basePath, '', $filePath), '\\/');
    }
}
