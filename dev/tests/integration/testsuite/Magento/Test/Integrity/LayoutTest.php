<?php
/**
 * Layout nodes integrity tests
 *
 * {license_notice}
 *
 * @category    tests
 * @package     integration
 * @subpackage  integrity
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Test_Integrity_LayoutTest extends PHPUnit_Framework_TestCase
{
    const NO_OVERRIDDEN_THEMES_MARKER = 'no-overriden-themes';

    /**
     * Cached lists of files
     *
     * @var array
     */
    protected static $_cachedFiles = array();

    public static function tearDownAfterClass()
    {
        self::$_cachedFiles = array(); // Free memory
    }

    /**
     * @param Magento_Core_Model_Theme $theme
     * @dataProvider areasAndThemesDataProvider
     */
    public function testHandlesHierarchy(Magento_Core_Model_Theme $theme)
    {
        $xml = $this->_composeXml($theme);

        /**
         * There could be used an xpath "/layouts/*[@type or @owner or @parent]", but it randomly produced bugs, by
         * selecting all nodes in depth. Thus it was refactored into manual nodes extraction.
         */
        $handles = array();
        foreach ($xml->children() as $handleNode) {
            if ($handleNode->getAttribute('type')
                || $handleNode->getAttribute('owner')
                || $handleNode->getAttribute('parent')
            ) {
                $handles[] = $handleNode;
            }
        }

        /** @var Magento_Core_Model_Layout_Element $node */
        $errors = array();
        foreach ($handles as $node) {
            $this->_collectHierarchyErrors($node, $xml, $errors);
        }

        if ($errors) {
            $this->fail("There are errors while checking the page type and fragment types hierarchy at:\n"
                . var_export($errors, 1)
            );
        }
    }

    /**
     * Composes full layout xml for designated parameters
     *
     * @param Magento_Core_Model_Theme $theme
     * @return Magento_Core_Model_Layout_Element
     */
    protected function _composeXml(Magento_Core_Model_Theme $theme)
    {
        /** @var Magento_Core_Model_Layout_Merge $layoutUpdate */
        $layoutUpdate = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Core_Model_Layout_Merge', array('theme' => $theme));
        return $layoutUpdate->getFileLayoutUpdatesXml();
    }

    /**
     * Validate node's declared position in hierarchy and add errors to the specified array if found
     *
     * @param SimpleXMLElement $node
     * @param Magento_Core_Model_Layout_Element $xml
     * @param array &$errors
     */
    protected function _collectHierarchyErrors($node, $xml, &$errors)
    {
        $name = $node->getName();
        $refName = $node->getAttribute('type') == Magento_Core_Model_Layout_Merge::TYPE_FRAGMENT
            ? $node->getAttribute('owner') : $node->getAttribute('parent');
        if ($refName) {
            $refNode = $xml->xpath("/layouts/{$refName}");
            if (!$refNode) {
                if ($refName == 'checkout_cart_configure' || $refName == 'checkout_cart_configurefailed') {
                    $this->markTestIncomplete('MAGETWO-9182');
                }
                $errors[$name][] = "Node '{$refName}', referenced in hierarchy, does not exist";
            } elseif ($refNode[0]->getAttribute('type') == Magento_Core_Model_Layout_Merge::TYPE_FRAGMENT) {
                $errors[$name][] = "Page fragment type '{$refName}', cannot be an ancestor in a hierarchy";
            }
        }
    }

    /**
     * List all themes available in the system
     *
     * A test that uses such data provider is supposed to gather view resources in provided scope
     * and analyze their integrity. For example, merge and verify all layouts in this scope.
     *
     * Such tests allow to uncover complicated code integrity issues, that may emerge due to view fallback mechanism.
     * For example, a module layout file is overlapped by theme layout, which has mistakes.
     * Such mistakes can be uncovered only when to emulate this particular theme.
     * Also emulating "no theme" mode allows to detect inversed errors: when there is a view file with mistake
     * in a module, but it is overlapped by every single theme by files without mistake. Putting question of code
     * duplication aside, it is even more important to detect such errors, than an error in a single theme.
     *
     * @return array
     */
    public function areasAndThemesDataProvider()
    {
        $result = array();
        $themeCollection = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Core_Model_Theme')->getCollection();
        /** @var $theme Magento_Core_Model_Theme */
        foreach ($themeCollection as $theme) {
            $result[] = array($theme);
        }
        return $result;
    }

    /**
     * @param Magento_Core_Model_Theme $theme
     * @dataProvider areasAndThemesDataProvider
     */
    public function testHandleLabels(Magento_Core_Model_Theme $theme)
    {
        $xml = $this->_composeXml($theme);

        $xpath = '/layouts/*['
            . '@type="' . Magento_Core_Model_Layout_Merge::TYPE_PAGE . '"'
            . ' or @type="' . Magento_Core_Model_Layout_Merge::TYPE_FRAGMENT . '"]';
        $handles = $xml->xpath($xpath) ?: array();

        /** @var Magento_Core_Model_Layout_Element $node */
        $errors = array();
        foreach ($handles as $node) {
            if (!$node->xpath('@label')) {
                $errors[] = $node->getName();
            }
        }
        if ($errors) {
            $this->fail("The following handles must have label, but they don't have it:\n" . var_export($errors, 1));
        }
    }

    /**
     * Check whether page types are declared only in layout update files allowed for it - base ones
     *
     * @dataProvider pageTypesDeclarationDataProvider
     */
    public function testPageTypesDeclaration(Magento_Core_Model_Layout_File $layout)
    {
        $content = simplexml_load_file($layout->getFilename());
        $this->assertEmpty(
            $content->xpath(Magento_Core_Model_Layout_Merge::XPATH_HANDLE_DECLARATION),
            "Theme layout update '" . $layout->getFilename() . "' contains page type declaration(s)"
        );
    }

    /**
     * Get theme layout updates
     *
     * @return Magento_Core_Model_Layout_File[]
     */
    public function pageTypesDeclarationDataProvider()
    {
        /** @var $themeUpdates Magento_Core_Model_Layout_File_Source_Theme */
        $themeUpdates = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Core_Model_Layout_File_Source_Theme');
        /** @var $themeUpdatesOverride Magento_Core_Model_Layout_File_Source_Override_Theme */
        $themeUpdatesOverride = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Core_Model_Layout_File_Source_Override_Theme');
        /** @var $themeCollection Magento_Core_Model_Theme_Collection */
        $themeCollection = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Core_Model_Theme_Collection');
        $themeCollection->addDefaultPattern('*');
        /** @var $themeLayouts Magento_Core_Model_Layout_File[] */
        $themeLayouts = array();
        /** @var $theme Magento_Core_Model_Theme */
        foreach ($themeCollection as $theme) {
            $themeLayouts = array_merge($themeLayouts, $themeUpdates->getFiles($theme));
            $themeLayouts = array_merge($themeLayouts, $themeUpdatesOverride->getFiles($theme));
        }
        $result = array();
        foreach ($themeLayouts as $layout) {
            $result[] = array($layout);
        }
        return $result;
    }

    /**
     * Check, that for an overriding file ($themeFile) in a theme ($theme), there is a corresponding base file
     *
     * @param Magento_Core_Model_Layout_File $themeFile
     * @param Magento_Core_Model_Theme $theme
     * @dataProvider overrideBaseFilesDataProvider
     */
    public function testOverrideBaseFiles($themeFile, $theme)
    {
        if ($themeFile === self::NO_OVERRIDDEN_THEMES_MARKER) {
            $this->markTestSkipped('No overriden themes.');
        }
        $baseFiles = self::_getCachedFiles($theme->getArea(), 'Magento_Core_Model_Layout_File_Source_Base', $theme);
        $fileKey = $themeFile->getModule() . '/' . $themeFile->getName();
        $this->assertArrayHasKey($fileKey, $baseFiles,
            sprintf("Could not find base file, overridden by theme file '%s'.", $themeFile->getFilename())
        );
    }

    /**
     * Check, that for an ancestor-overriding file ($themeFile) in a theme ($theme), there is a corresponding file
     * in that ancestor theme
     *
     * @param Magento_Core_Model_Layout_File $themeFile
     * @param Magento_Core_Model_Theme $theme
     * @dataProvider overrideThemeFilesDataProvider
     */
    public function testOverrideThemeFiles($themeFile, $theme)
    {
        if ($themeFile === self::NO_OVERRIDDEN_THEMES_MARKER) {
            $this->markTestSkipped('No overridden themes.');
        }
        // Find an ancestor theme, where a file is to be overridden
        $ancestorTheme = $theme;
        while ($ancestorTheme = $ancestorTheme->getParentTheme()) {
            if ($ancestorTheme == $themeFile->getTheme()) {
                break;
            }
        }
        $this->assertNotNull(
            $ancestorTheme,
            sprintf("Could not find ancestor theme '%s', its layout file is supposed to be overridden by file '%s'.",
                $themeFile->getTheme()->getCode(), $themeFile->getFilename())
        );

        // Search for the overridden file in the ancestor theme
        $ancestorFiles = self::_getCachedFiles($ancestorTheme->getFullPath(),
            'Magento_Core_Model_Layout_File_Source_Theme', $ancestorTheme);
        $fileKey = $themeFile->getModule() . '/' . $themeFile->getName();
        $this->assertArrayHasKey($fileKey, $ancestorFiles,
            sprintf("Could not find original file in '%s' theme, overridden by file '%s'.",
                $themeFile->getTheme()->getCode(), $themeFile->getFilename())
        );
    }

    /**
     * Retrieve list of cached source files
     *
     * @param string $cacheKey
     * @param string $sourceClass
     * @param Magento_Core_Model_Theme $theme
     * @return Magento_Core_Model_Layout_File[]
     */
    protected static function _getCachedFiles($cacheKey, $sourceClass, Magento_Core_Model_Theme $theme)
    {
        if (!isset(self::$_cachedFiles[$cacheKey])) {
            /* @var $fileList Magento_Core_Model_Layout_File[] */
            $fileList = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create($sourceClass)->getFiles($theme);
            $files = array();
            foreach ($fileList as $file) {
                $files[$file->getModule() . '/' . $file->getName()] = true;
            }
            self::$_cachedFiles[$cacheKey] = $files;
        }
        return self::$_cachedFiles[$cacheKey];
    }

    /**
     * @return array
     */
    public function overrideBaseFilesDataProvider()
    {
        return $this->_retrieveFilesForEveryTheme(
            Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Core_Model_Layout_File_Source_Override_Base')
        );
    }

    /**
     * @return array
     */
    public function overrideThemeFilesDataProvider()
    {
        return $this->_retrieveFilesForEveryTheme(
            Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Core_Model_Layout_File_Source_Override_Theme')
        );
    }

    /**
     * Scan all the themes in the system, for each theme retrieve list of files via $filesRetriever,
     * and return them as array of pairs [file, theme].
     *
     * @param Magento_Core_Model_Layout_File_SourceInterface $filesRetriever
     * @return array
     */
    protected function _retrieveFilesForEveryTheme(Magento_Core_Model_Layout_File_SourceInterface $filesRetriever)
    {
        $result = array();
        /** @var $themeCollection Magento_Core_Model_Theme_Collection */
        $themeCollection = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Core_Model_Theme_Collection');
        $themeCollection->addDefaultPattern('*');
        /** @var $theme Magento_Core_Model_Theme */
        foreach ($themeCollection as $theme) {
            foreach ($filesRetriever->getFiles($theme) as $file) {
                $result[] = array($file, $theme);
            }
        }
        return $result === array() ? array(array(self::NO_OVERRIDDEN_THEMES_MARKER, '')) : $result;
    }
}
