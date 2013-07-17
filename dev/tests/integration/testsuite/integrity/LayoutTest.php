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

class Integrity_LayoutTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param Mage_Core_Model_Theme $theme
     * @dataProvider areasAndThemesDataProvider
     */
    public function testHandlesHierarchy(Mage_Core_Model_Theme $theme)
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

        /** @var Mage_Core_Model_Layout_Element $node */
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
     * @param Mage_Core_Model_Theme $theme
     * @return Mage_Core_Model_Layout_Element
     */
    protected function _composeXml(Mage_Core_Model_Theme $theme)
    {
        /** @var Mage_Core_Model_Layout_Merge $layoutUpdate */
        $layoutUpdate = Mage::getModel(
            'Mage_Core_Model_Layout_Merge',
            array('arguments' => array('area' => $theme->getArea(), 'theme' => $theme))
        );
        return $layoutUpdate->getFileLayoutUpdatesXml();
    }

    /**
     * Validate node's declared position in hierarchy and add errors to the specified array if found
     *
     * @param SimpleXMLElement $node
     * @param Mage_Core_Model_Layout_Element $xml
     * @param array &$errors
     */
    protected function _collectHierarchyErrors($node, $xml, &$errors)
    {
        $name = $node->getName();
        $refName = $node->getAttribute('type') == Mage_Core_Model_Layout_Merge::TYPE_FRAGMENT
            ? $node->getAttribute('owner') : $node->getAttribute('parent');
        if ($refName) {
            $refNode = $xml->xpath("/layouts/{$refName}");
            if (!$refNode) {
                if ($refName == 'checkout_cart_configure' || $refName == 'checkout_cart_configurefailed') {
                    $this->markTestIncomplete('MAGETWO-9182');
                }
                $errors[$name][] = "Node '{$refName}', referenced in hierarchy, does not exist";
            } elseif ($refNode[0]->getAttribute('type') == Mage_Core_Model_Layout_Merge::TYPE_FRAGMENT) {
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
        $themeCollection = Mage::getModel('Mage_Core_Model_Theme')->getCollection();
        /** @var $theme Mage_Core_Model_Theme */
        foreach ($themeCollection as $theme) {
            if ($theme->getFullPath() == 'frontend/magento2/reference') {
                /** Skip the theme because of MAGETWO-9063 */
                continue;
            }
            $result[] = array($theme);
        }
        return $result;
    }

    /**
     * @param Mage_Core_Model_Theme $theme
     * @dataProvider areasAndThemesDataProvider
     */
    public function testHandleLabels(Mage_Core_Model_Theme $theme)
    {
        $xml = $this->_composeXml($theme);

        $xpath = '/layouts/*['
            . '@type="' . Mage_Core_Model_Layout_Merge::TYPE_PAGE . '"'
            . ' or @type="' . Mage_Core_Model_Layout_Merge::TYPE_FRAGMENT . '"'
            . ' or @translate="label"]';
        $handles = $xml->xpath($xpath) ?: array();

        /** @var Mage_Core_Model_Layout_Element $node */
        $errors = array();
        foreach ($handles as $node) {
            if (!$node->xpath('label')) {
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
    public function testPageTypesDeclaration(Mage_Core_Model_Layout_File $layout)
    {
        $content = simplexml_load_file($layout->getFilename());
        $this->assertEmpty(
            $content->xpath(Mage_Core_Model_Layout_Merge::XPATH_HANDLE_DECLARATION),
            "Theme layout update '" . $layout->getFilename() . "' contains page type declaration(s)"
        );
    }

    /**
     * Get theme layout updates
     *
     * @return Mage_Core_Model_Layout_File[]
     */
    public function pageTypesDeclarationDataProvider()
    {
        /** @var $themeUpdates Mage_Core_Model_Layout_File_Source_Theme */
        $themeUpdates = Mage::getModel('Mage_Core_Model_Layout_File_Source_Theme');
        /** @var $themeUpdatesOverride Mage_Core_Model_Layout_File_Source_Override_Theme */
        $themeUpdatesOverride = Mage::getModel('Mage_Core_Model_Layout_File_Source_Override_Theme');
        /** @var $themeCollection Mage_Core_Model_Theme_Collection */
        $themeCollection = Mage::getModel('Mage_Core_Model_Theme_Collection');
        $themeCollection->addDefaultPattern('*');
        /** @var $themeLayouts Mage_Core_Model_Layout_File[] */
        $themeLayouts = array();
        /** @var $theme Mage_Core_Model_Theme */
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
     * Check whether original files exist for corresponding overriding theme files
     *
     * @param Mage_Core_Model_Layout_File $file
     * @param Mage_Core_Model_Theme $theme
     * @dataProvider overrideThemeFilesDataProvider
     */
    public function testOverrideThemeFiles(Mage_Core_Model_Layout_File $file, Mage_Core_Model_Theme $theme)
    {
        $foundFile = false;
        /** @var $themeUpdates Mage_Core_Model_Layout_File_Source_Theme */
        $themeUpdates = Mage::getModel('Mage_Core_Model_Layout_File_Source_Theme');
        while ($theme = $theme->getParentTheme()) {
            if ($theme != $file->getTheme()) {
                continue;
            }
            $themeFiles = $themeUpdates->getFiles($theme);
            /** @var $themeFile Mage_Core_Model_Layout_File */
            foreach ($themeFiles as $themeFile) {
                if ($themeFile->getName() == $file->getName() && $themeFile->getModule() == $file->getModule()) {
                    $foundFile = true;
                    break 2;
                }
            }
        }
        $this->assertTrue($foundFile, sprintf("Could not find original file in '%s' theme overridden by file '%s'.",
            $file->getTheme()->getCode(), $file->getFilename()
        ));
    }

    /**
     * @return array
     */
    public function overrideThemeFilesDataProvider()
    {
        $result = array();
        /** @var $themeOverrides Mage_Core_Model_Layout_File_Source_Override_Theme */
        $themeOverrides = Mage::getModel('Mage_Core_Model_Layout_File_Source_Override_Theme');
        /** @var $themeCollection Mage_Core_Model_Theme_Collection */
        $themeCollection = Mage::getModel('Mage_Core_Model_Theme_Collection');
        $themeCollection->addDefaultPattern('*');
        /** @var $theme Mage_Core_Model_Theme */
        foreach ($themeCollection as $theme) {
            foreach ($themeOverrides->getFiles($theme) as $file) {
                $result[] = array($file, $theme);
            }
        }

        return $result;
    }
}
