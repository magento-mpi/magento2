<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Widget
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Test_Integrity_Magento_Widget_TemplateFilesTest extends PHPUnit_Framework_TestCase
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
        $blockFactory = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->get('Magento_Core_Model_BlockFactory');
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
        /** @var $model Magento_Widget_Model_Widget */
        $model = Mage::getModel('Magento_Widget_Model_Widget');
        foreach ($model->getWidgetsArray() as $row) {
            /** @var $instance Magento_Widget_Model_Widget_Instance */
            $instance = Mage::getModel('Magento_Widget_Model_Widget_Instance');
            $config = $instance->setType($row['type'])->getWidgetConfigAsArray();
            $class = $row['type'];
            if (is_subclass_of($class, 'Magento_Core_Block_Template')) {
                if (isset($config['parameters']) && isset($config['parameters']['template'])
                    && isset($config['parameters']['template']['values'])) {
                    $templates = $config['parameters']['template']['values'];
                    foreach ($templates as $template) {
                        if (isset($template['value'])) {
                            $result[] = array($class, (string)$template['value']);
                        }
                    }
                }
            }
        }
        return $result;
    }
}
