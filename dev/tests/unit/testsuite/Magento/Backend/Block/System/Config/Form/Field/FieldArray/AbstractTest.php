<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Block\System\Config\Form\Field\FieldArray;

class AbstractTest extends \PHPUnit_Framework_TestCase
{
    public function testGetArrayRows()
    {
        /** @var $block \Magento\Backend\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray */
        $block = $this->getMockForAbstractClass(
            'Magento\Backend\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray',
            array(),
            '',
            false,
            true,
            true,
            array('escapeHtml')
        );
        $block->expects($this->any())->method('escapeHtml')->will($this->returnArgument(0));

        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $element = $objectManager->getObject('Magento\Framework\Data\Form\Element\Multiselect');
        $element->setValue(array(array('test' => 'test', 'data1' => 'data1')));
        $block->setElement($element);
        $this->assertEquals(
            array(
                new \Magento\Framework\Object(
                    array(
                        'test' => 'test',
                        'data1' => 'data1',
                        '_id' => 0,
                        'column_values' => array('0_test' => 'test', '0_data1' => 'data1')
                    )
                )
            ),
            $block->getArrayRows()
        );
    }
}
