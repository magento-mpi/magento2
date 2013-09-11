<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Backend_Block_System_Config_Form_Field_FieldArray_AbstractTest extends PHPUnit_Framework_TestCase
{
    public function testGetArrayRows()
    {
        /** @var $block \Magento\Backend\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray */
        $block = $this->getMockForAbstractClass(
            '\Magento\Backend\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray',
            array(),
            '',
            false,
            true,
            true,
            array('escapeHtml')
        );
        $block->expects($this->any())
            ->method('escapeHtml')
            ->will($this->returnArgument(0));
        $element = new \Magento\Data\Form\Element\Multiselect();
        $element->setValue(array(
            array(
                'test' => 'test',
                'data1' => 'data1',
            )
        ));
        $block->setElement($element);
        $this->assertEquals(
            array(
                new \Magento\Object(array(
                    'test' => 'test',
                    'data1' => 'data1',
                    '_id' => 0,
                    'column_values' => array(
                        '0_test' => 'test',
                        '0_data1' => 'data1',
                    ),
                ))
            ),
            $block->getArrayRows()
        );
    }
}
