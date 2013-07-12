<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

class Theme_Reader
{
    /**
     * Root directory of the application
     *
     * @var string
     */
    protected $_rootDir;

    protected $_themeTrees = null;

    /**
     * @param string $rootDir
     */
    public function __construct($rootDir)
    {
        $this->_rootDir = $rootDir;
    }

    public function getThemeRelPath($themePath)
    {
        if (!preg_match('#/app/design/([^/]+/[^/]+/[^/]+)#', $themePath, $matches))
        {
            throw new Exception("Unable to extract relative theme path from non-theme path {$themePath}");
        }
        return $matches[1];
    }

    /**
     * Read all themes and form them into trees of parent->child
     *
     * @return array
     * @throws Exception
     */
    public function getAsTrees()
    {
        $this->_readThemesAsTrees();
        return $this->_themeTrees;
    }

    protected function _readThemesAsTrees()
    {
        if ($this->_themeTrees !== null) {
            return;
        }

        // Iterate on themes. On each iteration, select a theme, whose parent is already in a tree, add the theme
        $themes = $this->_readAllThemes();
        $processedThemes = array();
        $this->_themeTrees = array();
        while ($themes) {
            // Choose a theme, which can be added to the tree
            $processedCodes = array_keys($processedThemes);
            $chosenCode = null;
            foreach ($themes as $fullCode => $theme) {
                if (!$theme['parent'] || in_array($theme['area'] . '/' . $theme['parent'], $processedCodes)) {
                    $chosenCode = $fullCode;
                    break;
                }
            }
            if (!$chosenCode) {
                throw new Exception('Cannot choose theme to be processed next: ' . implode(',', array_keys($themes)));
            }

            // Add theme to the tree
            $theme = $themes[$chosenCode];
            $theme['children'] = array();
            if (!$theme['parent']) {
                $this->_themeTrees[$chosenCode] = $theme;
                $processedThemes[$chosenCode] = &$this->_themeTrees[$chosenCode];
            } else {
                $parentFullCode = $theme['area'] . '/' . $theme['parent'];
                $processedThemes[$parentFullCode]['children'][$chosenCode] = $theme;
                $processedThemes[$chosenCode] = &$processedThemes[$parentFullCode]['children'][$chosenCode];
            }

            unset($themes[$chosenCode]);
        }
    }

    /**
     * Read information about all the themes in the system
     */
    protected function _readAllThemes()
    {
        $themeFiles = glob($this->_rootDir . '/app/design/*/*/*/theme.xml');
        $result = array();
        foreach ($themeFiles as $themeFile)
        {
            $themeFile = strtr($themeFile, '\\', '/');
            $xml = new SimpleXMLElement(file_get_contents($themeFile));
            /** @var SimpleXMLElement $themeDeclaration */
            $themeDeclaration = $xml->package->theme;
            $attributes = $themeDeclaration->attributes();

            $themeCode = (string)$attributes['code'];

            $area = basename(dirname(dirname(dirname($themeFile))));

            $fullCode = $area . '/' . $themeCode;

            $relPath = $this->getThemeRelPath($themeFile);

            $result[$fullCode] = array(
                'area' => $area,
                'code' => $themeCode,
                'parent' => (string)@$attributes['parent'],
                'relPath' => $relPath,
                'path' => dirname($themeFile),
            );
        }

        return $result;
    }

    public function getThemeHierarchy($themeRelPath)
    {
        $this->_readThemesAsTrees();
        $themeHierarchy = $this->_getThemeHierarchy($this->_themeTrees, $themeRelPath);
        if (!$themeHierarchy) {
            throw new Exception("Couldn't find a theme by its relative path {$themeRelPath}");
        }
        return $themeHierarchy;
    }

    protected function _getThemeHierarchy($themeTrees, $findRelPath, $currentHierarchy = array())
    {
        foreach ($themeTrees as $theme) {
            if ($theme['relPath'] == $findRelPath) {
                $result = $currentHierarchy;
                $result[] = $theme;
                return $result;
            } elseif ($theme['children']) {
                $furtherHierarchy = $currentHierarchy;
                $furtherHierarchy[] = $theme;
                $result = $this->_getThemeHierarchy($theme['children'], $findRelPath, $furtherHierarchy);
                if ($result) {
                    return $result;
                }
            }
        }
        return array();
    }
}
