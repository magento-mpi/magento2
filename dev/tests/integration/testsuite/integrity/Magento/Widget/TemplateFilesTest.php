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

class Integrity_Magento_Widget_TemplateFilesTest extends PHPUnit_Framework_TestCase
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
        /** @var $model Magento_Widget_Model_Widget */
        $model = Mage::getModel('Magento_Widget_Model_Widget');
        foreach ($model->getWidgetsArray() as $row) {
<<<<<<< HEAD:dev/tests/integration/testsuite/integrity/Mage/Widget/TemplateFilesTest.php
            /** @var $instance Mage_Widget_Model_Widget_Instance */
            $instance = Mage::getModel('Mage_Widget_Model_Widget_Instance');
            $config = $instance->setType($row['type'])->getWidgetConfigAsArray();
            $class = $row['type'];
            if (is_subclass_of($class, 'Mage_Core_Block_Template')) {
                if (isset($config['parameters']) && isset($config['parameters']['template'])
                    && isset($config['parameters']['template']['values'])) {
                    $templates = $config['parameters']['template']['values'];
                    foreach ($templates as $template) {
                        if (isset($template['value'])) {
                            $result[] = array($class, (string)$template['value']);
                        }
                    }
=======
            /** @var $instance Magento_Widget_Model_Widget_Instance */
            $instance = Mage::getModel('Magento_Widget_Model_Widget_Instance');
            $config = $instance->setType($row['type'])->getWidgetConfig();
            $class = $row['type'];
            if (is_subclass_of($class, 'Magento_Core_Block_Template')) {
                $templates = $config->xpath('/widgets/' . $row['code'] . '/parameters/template/values/*/value');
                foreach ($templates as $template) {
                    $result[] = array($class, (string)$template);
>>>>>>> upstream/develop:dev/tests/integration/testsuite/integrity/Magento/Widget/TemplateFilesTest.php
                }
            }
        }
        return $result;
    }
}
