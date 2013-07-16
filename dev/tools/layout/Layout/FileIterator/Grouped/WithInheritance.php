<?php
class Layout_FileIterator_Grouped_WithInheritance implements IteratorAggregate
{
    protected $_innerIterator;
    protected $_layoutInheritance;
    protected $_layoutHelper;
    protected $_outerIterator = null;

    public function __construct(Layout_FileIterator_Grouped_ByModuleDirectory $filesIterator,
        Layout_Inheritance $layoutInheritance, Layout_Helper $layoutHelper)
    {
        $this->_innerIterator = $filesIterator;
        $this->_layoutInheritance = $layoutInheritance;
        $this->_layoutHelper = $layoutHelper;
    }

    public function getIterator()
    {
        if (!$this->_outerIterator) {
            $this->_createIteratorWithInheritedFiles();
        }
        return $this->_outerIterator;
    }

    protected function _createIteratorWithInheritedFiles()
    {
        $groups = array();
        foreach ($this->_innerIterator as $moduleDir => $files) {
            if ($this->_layoutInheritance->isThemePath($moduleDir)) {
                $files = $this->_addOldInheritedLayoutFiles($moduleDir, $files);
            }
            $groups[$moduleDir] = $files;
        }
        $this->_outerIterator = new ArrayIterator($groups);
    }

    protected function _addOldInheritedLayoutFiles($themeModuleDir, $themeFiles)
    {
        $result = $themeFiles;
        $inheritedFiles = $this->_layoutInheritance->getOldInheritedLayouts($themeModuleDir);
        foreach ($inheritedFiles as $inheritedFile) {
            $overridden = false;
            $inheritedRelName = $this->_layoutHelper->getRelName($inheritedFile);
            foreach ($themeFiles as $themeFile) {
                if ($this->_layoutHelper->getRelName($themeFile) == $inheritedRelName) {
                    $overridden = true;
                    break;
                }
            }
            if (!$overridden) {
                $result[] = $inheritedFile;
            }
        }
        return $result;
    }
}
