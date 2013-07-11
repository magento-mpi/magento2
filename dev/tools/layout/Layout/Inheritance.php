<?php
class Layout_Inheritance
{
    const APPEARS_BASE = 'base';
    const APPEARS_PARENT_THEME = 'parent';
    const APPEARS_NOT_FOUND = 'not found';

    protected $_themeReader;
    protected $_rootDir;

    public function __construct($rootDir, Theme_Reader $themeReader)
    {
        $this->_rootDir = $rootDir;
        $this->_themeReader = $themeReader;
    }

    public function isBasePath($path)
    {
        return strpos($path, 'app/code/') !== false;
    }

    public function isThemePath($path)
    {
        return strpos($path, 'app/design/') !== false;
    }

    public function getOverridingFilePath($handle, $originalDir)
    {
        if ($this->isBasePath($originalDir)) {
            return $this->_getOverridingPathInBase($handle, $originalDir);
        } elseif ($this->isThemePath($originalDir)) {
            return $this->_getOverridingPathInTheme($handle, $originalDir);
        } else {
            throw new Exception("Unrecognized base/theme/? path: {$originalDir}");
        }
    }

    protected function _getOverridingPathInBase($handle, $originalDir)
    {
        list($module, $area) = $this->_extractModuleAndArea($originalDir);
        return $this->_rootDir . '/app/code/' . implode('/', explode('_', $module))
            . "/view/{$area}/layout/{$handle}.xml";
    }

    protected function _extractModuleAndArea($path)
    {
        if ($this->isBasePath($path)) {
            if (!preg_match('#app/code/([^/]+/[^/]+)/view/([^/]+)#',$path, $matches)) {
                throw new Exception("Unable to extract module and area from base path {$path}");
            }
            $module = strtr($matches[1], '/', '_');
            $area = $matches[2];
        } elseif ($this->isThemePath($path)) {
            if (!preg_match('#app/design/([^/]+)/[^/]+/[^/]+/([A-Z][a-z]+_[A-Z][A-Za-z0-9]+)#',$path, $matches)) {
                throw new Exception("Unable to extract module and area from theme path {$path}");
            }
            $area = $matches[1];
            $module = $matches[2];
        } else {
            throw new Exception("Unrecognized base/theme/? path: {$path}");
        }
        return array($module, $area);
    }

    protected function _getOverridingPathInTheme($handle, $originalDir)
    {
        $appears = $this->_getWhereHandleAppears($handle, $originalDir);

        $layoutDir = $originalDir . '/layout';
        switch ($appears['code']) {
            case self::APPEARS_BASE:
                return $layoutDir . "/override/{$handle}.xml";
            case self::APPEARS_PARENT_THEME:
                $pathParts = explode('/', $appears['theme']['relPath']);
                $parentPackage = $pathParts[1];
                $parentTheme = $pathParts[2];
                return $layoutDir . "/override/{$parentPackage}/{$parentTheme}/{$handle}.xml";
                break;
            case self::APPEARS_NOT_FOUND:
                return $layoutDir . "/{$handle}.xml";
                break;
            default:
                throw new Exception("Unknown appears code: {$appears['code']}");
        }
    }

    protected function _getWhereHandleAppears($handle, $themeModuleDir)
    {
        $fallbackPaths = $this->_getThemeFallback($themeModuleDir);

        $code = self::APPEARS_NOT_FOUND;
        $theme = null;
        foreach ($fallbackPaths as $fallbackPath => $fallbackTheme) {
            $filePath = $fallbackPath . "/{$handle}.xml";
            if (file_exists($filePath)) {
                if ($fallbackTheme) {
                    $code = self::APPEARS_PARENT_THEME;
                    $theme = $fallbackTheme;
                } else {
                    $code = self::APPEARS_BASE;
                }
                break;
            }
        }
        return array('code' => $code, 'theme' => $theme);
    }

    protected function _getThemeFallback($themeModuleDir)
    {
        $result = array();

        // Add module base path
        list($module, $area) = $this->_extractModuleAndArea($themeModuleDir);
        $fallbackPath = $this->_rootDir . '/app/code/' . implode('/', explode('_', $module))
            . "/view/{$area}/layout";
        $result[$fallbackPath] = null;

        // Add parent theme base paths
        $themeRelPath = $this->_themeReader->getThemeRelPath($themeModuleDir);
        $hierarchy = $this->_themeReader->getThemeHierarchy($themeRelPath);
        $hierarchy = array_reverse($hierarchy);
        array_shift($hierarchy); // Remove current child theme
        foreach ($hierarchy as $theme) {
            $fallbackPath = $theme['path'] . "/{$module}/layout";
            $result[$fallbackPath] = $theme;
        }

        return $result;
    }

    public function getInheritedHandles($themeModuleDir)
    {
        if (!$this->isThemePath($themeModuleDir)) {
            throw new Exception('Not a theme path: ' . $themeModuleDir);
        }

        $result = array();
        $fallback = $this->_getThemeFallback($themeModuleDir);
        foreach (array_keys($fallback) as $layoutPath) {
            foreach (glob($layoutPath . '/*.xml') as $file) {
                $result[] = basename($file, '.xml');
            }
        }
        return $result;
    }

    public function getOldInheritedLayouts($themeModuleDir)
    {
        if (!$this->isThemePath($themeModuleDir)) {
            throw new Exception('Not a theme path: ' . $themeModuleDir);
        }

        $result = array();
        $fallback = $this->_getThemeFallback($themeModuleDir);
        foreach (array_keys($fallback) as $newLayoutPath) {
            $oldLayoutPath = dirname($newLayoutPath);
            foreach (glob($oldLayoutPath . '/*.xml') as $file) {
                if (!$this->_isLayoutFile($file)) {
                    continue;
                }

                $basename = basename($file);
                if (isset($result[$basename])) {
                    continue;
                }
                $result[$basename] = $file;
            }
        }
        return array_values($result);
    }

    protected function _isLayoutFile($filePath)
    {
        return strpos(file_get_contents($filePath), '<layout') !== false;
    }
}
