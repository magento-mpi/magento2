<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @group integrity
 */
class Integrity_Modular_TemplateFilesTest extends Magento_Test_TestCase_IntegrityAbstract
{
    /**
     * @param string $module
     * @param string $template
     * @param string $class
     * @param string $area
     * @dataProvider allTemplatesDataProvider
     */
    public function testAllTemplates($module, $template, $class, $area)
    {
        $params = array(
            'area'    => $area,
            'package' => false, // intentionally to make sure the module files will be requested
            'theme'   => false,
            'module'  => $module
        );
        $file = Mage::getDesign()->getFilename($template, $params);
        $this->assertFileExists($file, "Block class: {$class}");
    }

    /**
     * @return array
     */
    public function allTemplatesDataProvider()
    {
        $templates = array();
        foreach (Utility_Classes::collectModuleClasses('Block') as $blockClass => $module) {
            if (!in_array($module, $this->_getEnabledModules())) {
                continue;
            }
            $class = new ReflectionClass($blockClass);
            if ($class->isAbstract() || !$class->isSubclassOf('Mage_Core_Block_Template')) {
                continue;
            }

            $area = 'frontend';
            if ($module == 'Mage_Install') {
                $area = 'install';
            } elseif ($module == 'Mage_Adminhtml' || strpos($blockClass, '_Adminhtml_')
                || strpos($blockClass, '_Backend_')
                || ($this->_isClassInstanceOf($blockClass, 'Mage_Backend_Block_Template'))
            ) {
                $area = 'adminhtml';
            }

            Mage::getConfig()->setCurrentAreaCode($area);

            $block = new $blockClass;
            $template = $block->getTemplate();
            if ($template) {
                $templates[] = array($module, $template, $blockClass, $area);
            }
        }
        return $templates;
    }

    /**
     * @param string $blockClass
     * @param string $parentClass
     * @return bool
     */
    protected function _isClassInstanceOf($blockClass, $parentClass)
    {
        $currentClass = new ReflectionClass($blockClass);
        $supertypes = array();
        do {
            $supertypes = array_merge($supertypes, $currentClass->getInterfaceNames());
            if (!($currentParent = $currentClass->getParentClass())) {
                break;
            }
            $supertypes[] = $currentParent->getName();
            $currentClass = $currentParent;
        } while (true);

        return in_array($parentClass, $supertypes);
    }
}
