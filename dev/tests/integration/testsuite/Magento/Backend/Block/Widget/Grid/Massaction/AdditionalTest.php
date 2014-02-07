<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Block\Widget\Grid\Massaction;

class AdditionalTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @magentoAppArea adminhtml
     */
    public function testToHtml()
    {
        $interpreter = $this->getMock('Magento\View\Layout\Argument\Interpreter\Options', array(), array(), '', false);
        /**
         * @var Additional $block
         */
        $block = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Backend\Block\Widget\Grid\Massaction\Additional',
            array('optionsInterpreter' => $interpreter)
        );
        $modelClass = 'Magento\Backend\Block\Widget\Grid\Massaction';
        $data = array(
            'fields' => array(
                'field1' => array(
                    'type' => 'select',
                    'values' => $modelClass,
                    'class' => 'custom_class',
                ),
            ),
        );
        $block->setData($data);
        $evaluatedValues = array(
            array('value' => 'value1', 'label' => 'label 1'),
            array('value' => 'value2', 'label' => 'label 2'),
        );
        $interpreter->expects($this->once())
            ->method('evaluate')
            ->with(array('model' => $modelClass))
            ->will($this->returnValue($evaluatedValues));

        $html = $block->toHtml();
        $this->assertStringMatchesFormat(
            '%acustom_class absolute-advice%avalue="value1"%slabel 1%avalue="value2"%slabel 2%a',
            $html
        );
    }
}
