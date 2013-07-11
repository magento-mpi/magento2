<?php
class Layout_FileIterator_ByThemeInheritance implements IteratorAggregate
{
    protected $_themeFilesIterator;
    protected $_themeReader;
    protected $_outerIterator = null;

    public function __construct(Traversable $themeFilesIterator, Theme_Reader $themeReader)
    {
        $this->_themeFilesIterator = $themeFilesIterator;
        $this->_themeReader = $themeReader;
    }

    public function getIterator()
    {
        if (!$this->_outerIterator) {
            $this->_orderFilesToOuterIterator();
        }
        return $this->_outerIterator;
    }

    protected function _orderFilesToOuterIterator()
    {
        // Order themes, so that child themes are after parent themes
        $themes = $this->_themeReader->getAsTrees();
        $themeList = $this->_themeTrees2PlainList($themes);

        // Add empty filelists to the themes
        foreach ($themeList as &$theme) {
            $theme['files'] = array();
        }
        unset($theme);

        // Fill themes file lists
        foreach ($this->_themeFilesIterator as $file) {
            $themeRelPath = $this->_themeReader->getThemeRelPath($file);
            $themeList[$themeRelPath]['files'][] = $file;
        }

        // Merge all theme file lists.
        // Thus now all the files are ordered - files from child themes are always after the files from parent themes
        $orderedFiles = array();
        foreach ($themeList as $theme) {
            $orderedFiles = array_merge($orderedFiles, $theme['files']);
        }

        // Result
        $this->_outerIterator = new ArrayIterator($orderedFiles);
    }

    protected function _themeTrees2PlainList($themes)
    {
        $result = array();
        foreach ($themes as $theme) {
            $result[$theme['relPath']] = $theme;
            if ($theme['children']) {
                $childThemeList = $this->_themeTrees2PlainList($theme['children']);
                $result = array_merge($result, $childThemeList);
            }
        }
        return $result;
    }
}
