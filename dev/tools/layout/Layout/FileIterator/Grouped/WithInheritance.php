<?php
class Layout_FileIterator_Grouped_WithInheritance implements IteratorAggregate
{
    protected $_layoutInheritance;
    protected $_innerIterator;
    protected $_outerIterator = null;

    public function __construct(Layout_FileIterator_Grouped_ByDirectory $filesIterator,
        Layout_Inheritance $layoutInheritance)
    {
        $this->_innerIterator = $filesIterator;
        $this->_layoutInheritance = $layoutInheritance;
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
        foreach ($this->_innerIterator as $dir => $files) {
            if ($this->_layoutInheritance->isThemePath($dir)) {
                $files = $this->_addOldInheritedLayoutFiles($dir, $files);
            }
            $groups[$dir] = $files;
        }
        $this->_outerIterator = new ArrayIterator($groups);
    }

    protected function _addOldInheritedLayoutFiles($themeModuleDir, $themeFiles)
    {
        $result = $themeFiles;
        $inheritedFiles = $this->_layoutInheritance->getOldInheritedLayouts($themeModuleDir);
        foreach ($inheritedFiles as $inheritedFile) {
            $overridden = false;
            $inheritedBasename = basename($inheritedFile);
            foreach ($themeFiles as $themeFile) {
                if (basename($themeFile) == $inheritedBasename) {
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
