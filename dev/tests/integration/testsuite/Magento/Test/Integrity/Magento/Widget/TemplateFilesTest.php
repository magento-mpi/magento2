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
        /** @var $blockFactory \Magento\Core\Model\BlockFactory */
        $blockFactory = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->get('Magento\Core\Model\BlockFactory');
        /** @var \Magento\Core\Block\Template $block */
        $block = $blockFactory->createBlock($class);
        $this->assertInstanceOf('Magento\Core\Block\Template', $block);
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
        /** @var $model \Magento\Widget\Model\Widget */
        $model = Mage::getModel('Magento\Widget\Model\Widget');
        foreach ($model->getWidgetsArray() as $row) {
            /** @var $instance \Magento\Widget\Model\Widget\Instance */
            $instance = Mage::getModel('Magento\Widget\Model\Widget\Instance');
            $config = $instance->setType($row['type'])->getWidgetConfig();
            $class = $row['type'];
            if (is_subclass_of($class, 'Magento\Core\Block\Template')) {
                $templates = $config->xpath('/widgets/' . $row['code'] . '/parameters/template/values/*/value');
                foreach ($templates as $template) {
                    $result[] = array($class, (string)$template);
                }
            }
        }
        return $result;
    }
}
