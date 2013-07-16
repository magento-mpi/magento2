<?php
class Layout_Handle_Separator
{
    protected $_layoutGroupIterator;
    protected $_layoutAnalyzer;
    protected $_layoutInheritance;
    protected $_emptyHandleTemplate;

    public function __construct(Traversable $layoutGroupIterator, Layout_Analyzer $layoutAnalyzer,
        Layout_Inheritance $layoutInheritance, $emptyHandleTemplate)
    {
        $this->_layoutGroupIterator = $layoutGroupIterator;
        $this->_layoutAnalyzer = $layoutAnalyzer;
        $this->_layoutInheritance = $layoutInheritance;
        $this->_emptyHandleTemplate = $emptyHandleTemplate;
    }

    /**
     * Go through all the layout files in the system and break them down into handles at appropriate places
     */
    public function performTheJob()
    {
        foreach ($this->_layoutGroupIterator as $moduleDir => $layoutFilesGroup) {
            $handleContents = $this->_layoutAnalyzer->aggregateHandles($layoutFilesGroup);
            $overriddenHandles = array_keys($handleContents);
            $fullyInheritedHandles = $this->_getFullyInheritedHandles($moduleDir, $layoutFilesGroup);

            /// Put overriding handles to the appropriate locations
            foreach ($handleContents as $handle => $fileContents) {
                if (in_array($handle, $fullyInheritedHandles)) {
                    continue; // No need to duplicate the handle, which can be inherited from parents
                }
                $this->_overrideHandle($handle, $fileContents, $moduleDir);
            }

            // Wipe the handles that did not exist in the processed files, but were present in inherited themes/bases
            // I.e. previously, theme just removed them
            if ($this->_layoutInheritance->isThemePath($moduleDir)) {
                $inheritedHandles = $this->_layoutInheritance->getInheritedHandles($moduleDir);
                $emptiedHandles = array_diff($inheritedHandles, $overriddenHandles);
                foreach ($emptiedHandles as $handle) {
                    $emptyFileContents = sprintf($this->_emptyHandleTemplate, $handle);
                    $this->_overrideHandle($handle, $emptyFileContents, $moduleDir);
                }
            }
        }
    }

    protected function _getFullyInheritedHandles($dir, $files)
    {
        if ($this->_layoutInheritance->isBasePath($dir)) {
            return array();
        }

        $themePath = $dir . '/';
        $declaredHandles = array();
        $inheritedHandles = array();
        foreach ($files as $file) {
            $handles = $this->_layoutAnalyzer->getHandles($file);
            if (substr($file, 0, strlen($themePath)) == $themePath) {
                $declaredHandles = array_merge($declaredHandles, $handles);
            } else {
                $inheritedHandles = array_merge($inheritedHandles, $handles);
            }
        }

        return array_diff($inheritedHandles, $declaredHandles);
    }

    protected function _overrideHandle($handle, $fileContents, $originalDir)
    {
        $filePath = $this->_layoutInheritance->getOverridingFilePath($handle, $originalDir);
        $handleDir = dirname($filePath);
        if (!is_dir($handleDir)) {
            mkdir($handleDir, 0666, true);
        }
        file_put_contents($filePath, $fileContents);
    }
}
