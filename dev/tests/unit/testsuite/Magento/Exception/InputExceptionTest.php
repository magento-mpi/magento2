<?php
namespace Magento\Exception;

class InputExceptionTest extends \PHPUnit_Framework_TestCase
{

    public function test()
    {
        // test constructor
        $inputException = new InputException('product.quantity', InputException::INVALID_FIELD_RANGE, [
            'minValue' => 0,
            'value'    => -100
        ]);

        $this->assertSame(InputException::INPUT_EXCEPTION, $inputException->getCode());
        $this->assertStringStartsWith('One or more', $inputException->getMessage());
        $this->assertSame(
            [
                [
                    'minValue'  => 0,
                    'value'     => -100,
                    'fieldName' => 'product.quantity',
                    'code'      => InputException::INVALID_FIELD_RANGE,
                ]
            ],
            $inputException->getParams()
        );

        // test addError
        $inputException->addError('product.name', InputException::EMPTY_FIELD_REQUIRED);

        $this->assertSame(InputException::INPUT_EXCEPTION, $inputException->getCode());
        $this->assertStringStartsWith('One or more', $inputException->getMessage());
        $this->assertSame(
            [
                [
                    'minValue'  => 0,
                    'value'     => -100,
                    'fieldName' => 'product.quantity',
                    'code'      => InputException::INVALID_FIELD_RANGE,
                ],
                [
                    'fieldName' => 'product.name',
                    'code'      => InputException::EMPTY_FIELD_REQUIRED,
                ]
            ],
            $inputException->getParams()
        );
    }
}
 