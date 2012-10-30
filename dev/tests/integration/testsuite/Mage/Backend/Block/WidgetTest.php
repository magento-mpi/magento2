<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Backend
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Mage_Backend_Block_Widget
 *
 * @group module:Mage_Backend
 */
class Mage_Backend_Block_WidgetTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers Mage_Backend_Block_Widget::getButtonHtml
     */
    public function testGetButtonHtml()
    {
        $layout = new Mage_Core_Model_Layout(array('area' => 'adminhtml'));
        $layout->getUpdate()->load();
        $layout->generateXml()->generateElements();

        $widget = new Mage_Backend_Block_Widget();
        $widget->setLayout($layout);

        $this->assertRegExp(
            '/<button.*onclick\=\"this.form.submit\(\)\".*\>[\s\S]*Button Label[\s\S]*<\/button>/iu',
            $widget->getButtonHtml('Button Label', 'this.form.submit()')
        );
    }

    /**
     * Case when two buttons will be created in same parent block
     *
     * @covers Mage_Backend_Block_Widget::getButtonHtml
     */
    public function testGetButtonHtmlForTwoButtonsInOneBlock()
    {
        $layout = new Mage_Core_Model_Layout(array('area' => 'adminhtml'));
        $layout->getUpdate()->load();
        $layout->generateXml()->generateElements();

        $widget = new Mage_Backend_Block_Widget();
        $widget->setLayout($layout);

        $this->assertRegExp(
            '/<button.*onclick\=\"this.form.submit\(\)\".*\>[\s\S]*Button Label[\s\S]*<\/button>/iu',
            $widget->getButtonHtml('Button Label', 'this.form.submit()')
        );

        $this->assertRegExp(
            '/<button.*onclick\=\"this.form.submit\(\)\".*\>[\s\S]*Button Label2[\s\S]*<\/button>/iu',
            $widget->getButtonHtml('Button Label2', 'this.form.submit()')
        );
    }

    public function testGetSuffixId()
    {
        $block = Mage::getObjectManager()->create('Mage_Backend_Block_Widget');
        $this->assertStringEndsNotWith('_test', $block->getSuffixId('suffix'));
        $this->assertStringEndsWith('_test', $block->getSuffixId('test'));
    }
}
