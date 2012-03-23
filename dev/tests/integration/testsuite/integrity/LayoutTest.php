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
     * @param string $area
     * @param string $package
     * @param string $theme
     * @dataProvider handleHierarchyDataProvider
     */
    public function testHandleHierarchy($area, $package, $theme)
    {
        $layoutUpdate = new Mage_Core_Model_Layout_Update(array(
            'area' => $area, 'package' => $package, 'theme' => $theme
        ));
        $xml = $layoutUpdate->getFileLayoutUpdatesXml();
        $handles = $xml->xpath('/layouts/*[@parent]') ?: array();
        /** @var Mage_Core_Model_Layout_Element $node */
        $errors = array();
        foreach ($handles as $node) {
            $parent = $node->getAttribute('parent');
            if (!$xml->xpath("/layouts/{$parent}")) {
                $errors[$node->getName()] = $parent;
            }
        }
        if ($errors) {
            $this->fail("Reference(s) to non-existing parent handle found at:\n" . var_export($errors, 1));
        }
    }

    /**
     * @return array
     */
    public function handleHierarchyDataProvider()
    {
        $result = array();
        foreach (array('adminhtml', 'frontend', 'install') as $area) {
            $result[] = array($area, false, false);
            foreach (Mage::getDesign()->getDesignEntitiesStructure($area, false) as $package => $themes) {
                foreach (array_keys($themes) as $theme) {
                    $result[] = array($area, $package, $theme);
                }
            }
        }
        return $result;
    }
}
