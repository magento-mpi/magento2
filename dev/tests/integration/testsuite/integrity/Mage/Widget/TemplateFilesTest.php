<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Widget
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Integrity_Mage_Widget_TemplateFilesTest extends PHPUnit_Framework_TestCase
{
    /**
     * Check if all the declared widget templates actually exist
     *
     * @param string $class
     * @param string $template
     * @dataProvider widgetTemplatesDataProvider
     */
    public function testWidgetTemplates($class, $template)
    {
        /** @var $blockFactory Magento_Core_Model_BlockFactory */
        $blockFactory = Mage::getObjectManager()->get('Magento_Core_Model_BlockFactory');
        /** @var Magento_Core_Block_Template $block */
        $block = $blockFactory->createBlock($class);
        $this->assertInstanceOf('Magento_Core_Block_Template', $block);
        $block->setTemplate((string)$template);
        $this->assertFileExists($block->getTemplateFile());
    }

    /**
     * Collect all declared widget blocks and templates
     *
     * @return array
     */
    public function widgetTemplatesDataProvider()
    {
        $result = array();
        /** @var $model Mage_Widget_Model_Widget */
        $model = Mage::getModel('Mage_Widget_Model_Widget');
        foreach ($model->getWidgetsArray() as $row) {
            /** @var $instance Mage_Widget_Model_Widget_Instance */
            $instance = Mage::getModel('Mage_Widget_Model_Widget_Instance');
            $config = $instance->setType($row['type'])->getWidgetConfig();
            $class = $row['type'];
            if (is_subclass_of($class, 'Magento_Core_Block_Template')) {
                $templates = $config->xpath('/widgets/' . $row['code'] . '/parameters/template/values/*/value');
                foreach ($templates as $template) {
                    $result[] = array($class, (string)$template);
                }
            }
        }
        return $result;
    }
}
