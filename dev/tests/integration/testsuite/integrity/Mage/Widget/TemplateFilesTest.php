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

/**
 * @group module:Mage_Widget
 * @group integrity
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
        $block = new $class;
        /** @var Mage_Core_Block_Template $block */
        $this->assertInstanceOf('Mage_Core_Block_Template', $block);
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
        $model = new Mage_Widget_Model_Widget;
        foreach ($model->getWidgetsArray() as $row) {
            $instance = new Mage_Widget_Model_Widget_Instance;
            $config = $instance->setType($row['type'])->getWidgetConfig();
            $class = Mage::getConfig()->getBlockClassName($row['type']);
            if (is_subclass_of($class, 'Mage_Core_Block_Template')) {
                $templates = $config->xpath('/widgets/' . $row['code'] . '/parameters/template/values/*/value');
                foreach ($templates as $template) {
                    $result[] = array($class, (string)$template);
                }
            }
        }
        return $result;
    }
}
