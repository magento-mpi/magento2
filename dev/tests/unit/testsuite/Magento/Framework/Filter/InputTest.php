<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\Filter;

class InputTest extends \PHPUnit_Framework_TestCase
{
    public function testFilterZendFilterAsObject()
    {
        $objectManagerMock = $this->getMock('Magento\Framework\ObjectManagerInterface');
        $inputFilter = new Input($objectManagerMock);

        /** @var \Zend_Filter_Interface $filterMock */
        /** This filter should be applied to 'field1' field value only */
        $filterMock = $this->getMock('Zend_Filter_Interface', [], [], '', false);
        $filterMock->expects($this->exactly(1))->method('filter')->will(
            $this->returnCallback(
                function ($input) {
                    return '(' . $input . ')';
                }
            )
        );
        $inputFilter->addFilter('field1', $filterMock);

        /** Execute SUT and ensure that array items were filtered correctly */
        $inputArray = ['field1' => 'value1', 'field2' => 'value2'];
        $expectedOutput = ['field1' => '(value1)', 'field2' => 'value2'];
        $this->assertEquals($expectedOutput, $inputFilter->filter($inputArray), 'Array was filtered incorrectly.');
    }

    public function testFilterZendFilterAsArray()
    {
        $objectManagerMock = $this->getMock('Magento\Framework\ObjectManagerInterface');
        $inputFilter = new Input($objectManagerMock);

        /** This filter should be applied to 'field1' field value only */
        $inputFilter->setFilters(
            array(
                'field1' => array(
                    array(
                        'zend' => 'StringToUpper',
                        'args' => array('encoding' => 'utf-8')
                    )
                )
            )
        );

        /** Execute SUT and ensure that array items were filtered correctly */
        $inputArray = ['field1' => 'value1', 'field2' => 'value2'];
        $expectedOutput = ['field1' => 'VALUE1', 'field2' => 'value2'];
        $this->assertEquals($expectedOutput, $inputFilter->filter($inputArray), 'Array was filtered incorrectly.');
    }
}
