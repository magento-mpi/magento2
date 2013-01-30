<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Theme data helper
 */
class Mage_Core_Helper_Theme extends Mage_Core_Helper_Abstract
{
    /**
     * Object manager
     *
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * Directories
     *
     * @var Mage_Core_Model_Dir
     */
    protected $_dirs;

    /**
     * Constructor
     *
     * @param Magento_ObjectManager $objectManager
     */
    public function __construct(Magento_ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
        $this->_dirs = $this->_objectManager->get('Mage_Core_Model_Dir');
    }

    /**
     * Get CSS files of a given theme
     *
     * Returned array has a structure
     * array(
     *   'Mage_Catalog::widgets.css' => 'http://mage2.com/pub/media/theme/frontend/_theme15/en_US/Mage_Cms/widgets.css'
     * )
     *
     * @param Mage_Core_Model_Theme $theme
     * @return array
     */
    public function getCssFiles($theme)
    {
        $arguments = array(
            'area'  => $theme->getArea(),
            'theme' => $theme->getId()
        );
        /** @var $layoutMerge Mage_Core_Model_Layout_Merge */
        $layoutMerge = Mage::getModel('Mage_Core_Model_Layout_Merge', array('arguments' => $arguments));
        $layoutElement = $layoutMerge->getFileLayoutUpdatesXml();

        $xpathRefs = '//reference[@name="head"]/action[@method="addCss" or @method="addCssIe"]/*[1]';
        $xpathBlocks = '//block[@type="Mage_Page_Block_Html_Head"]/action[@method="addCss" or @method="addCssIe"]/*[1]';
        $elements = array_merge(
            $layoutElement->xpath($xpathRefs),
            $layoutElement->xpath($xpathBlocks)
        );

        $design = Mage::getDesign();
        $params = array(
            'area'       => $theme->getArea(),
            'themeModel' => $theme,
            'skipProxy'  => true
        );

        $basePath = $this->_dirs->getDir(Mage_Core_Model_Dir::APP);
        $files = array();
        foreach ($elements as $fileId) {
            $fileId = (string)$fileId;
            $file = array(
                'id'       => $fileId,
                'path'     => $design->getViewFile($fileId, $params),
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
     * @param Mage_Core_Model_Theme $theme
     * @return array
     * @throws Mage_Core_Exception
     */
    public function getGroupedCssFiles($theme)
    {
        $jsDir = $this->_dirs->getDir(Mage_Core_Model_Dir::PUB_LIB);
        $codeDir = $this->_dirs->getDir(Mage_Core_Model_Dir::MODULES);
        $designDir = $this->_dirs->getDir(Mage_Core_Model_Dir::THEMES);
        $basePath = $this->_dirs->getDir(Mage_Core_Model_Dir::APP);

        $groups = array();
        $themes = array();
        foreach ($this->getCssFiles($theme) as $file) {
            $this->_detectTheme($file, $designDir);
            $this->_detectGroup($file);

            if (isset($file['theme']) && $file['theme']->getId()) {
                $themes[$file['theme']->getId()] = $file['theme'];
            }

            if (!isset($file['group'])) {
                Mage::throwException(
                    $this->__('Group is missed for file "%s"', $file['safePath'])
                );
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
            /** @var $fileTheme Mage_Core_Model_Theme */
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
     * @throws InvalidArgumentException
     * @return Mage_Theme_Helper_Data
     */
    protected function _detectTheme(&$file, $designDir)
    {
        //TODO use cache here, so files of the same theme share one model

        $isInsideDesignDir = substr($file['path'], 0, strlen($designDir)) == $designDir;
        if (!$isInsideDesignDir) {
            return $this;
        }

        $relativePath = substr($file['path'], strlen($designDir));

        $area = strtok($relativePath, DIRECTORY_SEPARATOR);
        $package = strtok(DIRECTORY_SEPARATOR);
        $theme = strtok(DIRECTORY_SEPARATOR);

        if ($area === false || $package === false || $theme === false) {
            Mage::throwException($this->__('Theme path "%s/%s/%s" is incorrect', $area, $package, $theme));
        }
        /** @var $collection Mage_Core_Model_Resource_Theme_Collection */
        $collection = $this->_objectManager->create('Mage_Core_Model_Resource_Theme_Collection');
        $themeModel = $collection->getThemeByFullPath($area . '/' . $package . '/' . $theme);

        if (!$themeModel || !$themeModel->getId()) {
            Mage::throwException($this->__('Invalid theme loaded by theme path "%s/%s/%s"', $area, $package, $theme));
        }

        $file['theme'] = $themeModel;

        return $this;
    }

    /**
     * Detect group where file should be placed and set it to file data under "group" key
     *
     * @param array $file
     * @return Mage_Theme_Helper_Data
     * @throws Mage_Core_Exception
     */
    protected function _detectGroup(&$file)
    {
        $jsDir = $this->_dirs->getDir(Mage_Core_Model_Dir::PUB_LIB);
        $codeDir = $this->_dirs->getDir(Mage_Core_Model_Dir::MODULES);
        $designDir = $this->_dirs->getDir(Mage_Core_Model_Dir::THEMES);
        $basePath = $this->_dirs->getDir(Mage_Core_Model_Dir::APP);

        $group = null;
        if (substr($file['path'], 0, strlen($designDir)) == $designDir) {
            if (!isset($file['theme']) || !$file['theme']->getId()) {
                Mage::throwException(
                    $this->__('Theme is missed for file "%s"', $file['safePath'])
                );
            }
            $group = $file['theme']->getThemeId();
        } elseif (substr($file['path'], 0, strlen($jsDir)) == $jsDir) {
            $group = $jsDir;
        } elseif (substr($file['path'], 0, strlen($codeDir)) == $codeDir) {
            $group = $codeDir;
        } else {
            Mage::throwException(
                $this->__('Invalid view file directory "%s"', $file['safePath'])
            );
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
            $jsDir => $this->__('Library files'),
            $codeDir => $this->__('Framework files')
        );
        foreach ($themes as $theme) {
            /** @var $theme Mage_Core_Model_Theme */
            $labels[$theme->getThemeId()] = $this->__('"%s" Theme files', $theme->getThemeTitle());
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
     * @param Mage_Core_Model_Theme $firstTheme
     * @param Mage_Core_Model_Theme $secondTheme
     * @return int
     */
    protected function _sortThemesByHierarchyCallback($firstTheme, $secondTheme)
    {
        $parentTheme = $firstTheme->getParentTheme();
        while ($parentTheme) {
            if ($parentTheme->getId() == $secondTheme->getId()) {
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
     * @return string
     */
    public function getSafePath($filePath, $basePath)
    {
        return str_ireplace($basePath, '', $filePath);
    }
}
