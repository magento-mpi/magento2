<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Exception;

/**
 * Class InputExceptionTest
 *
 * @package Magento\Exception
 */
class InputExceptionTest extends \PHPUnit_Framework_TestCase
{

    public function testConstructor()
    {
        $inputException = InputException::create(
            InputException::INVALID_FIELD_RANGE,
            'quantity',
            -100,
            ['minValue' => 0]
        );

        $this->assertEquals(InputException::INPUT_EXCEPTION, $inputException->getCode());
        $this->assertStringStartsWith('One or more', $inputException->getMessage());
        $this->assertEquals(
            [
                [
                    'minValue'  => 0,
                    'value'     => -100,
                    'fieldName' => 'quantity',
                    'code'      => InputException::INVALID_FIELD_RANGE,
                ]
            ],
            $inputException->getParams()
        );
    }

    public function testAddError()
    {
        $inputException = InputException::create(
            InputException::INVALID_FIELD_RANGE,
            'weight',
            -100,
            ['minValue' => 1]
        );

        $inputException->addError(
            InputException::REQUIRED_FIELD,
            'name',
            ''
        );

        $this->assertEquals(InputException::INPUT_EXCEPTION, $inputException->getCode());
        $this->assertStringStartsWith('One or more', $inputException->getMessage());
        $this->assertEquals(
            [
                [
                    'minValue'  => 1,
                    'value'     => -100,
                    'fieldName' => 'weight',
                    'code'      => InputException::INVALID_FIELD_RANGE,
                ],
                [
                    'fieldName' => 'name',
                    'code'      => InputException::REQUIRED_FIELD,
                    'value'     => '',
                ]
            ],
            $inputException->getParams()
        );
    }
}
