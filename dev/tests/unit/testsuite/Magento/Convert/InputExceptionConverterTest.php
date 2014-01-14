<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Convert;

use Magento\Exception\InputException;

class InputExceptionConverterTest extends \PHPUnit_Framework_TestCase
{
    public function testToString()
    {
        $exception = InputException::create(InputException::INVALID_FIELD_VALUE, 'name', 14)
            ->addError(InputException::REQUIRED_FIELD, 'fieldRequired', '');
        $converter = new InputExceptionConverter();

        $message = $converter->toString($exception);

        $this->assertEquals(
            'Invalid value of "14" provided for name field. fieldRequired is a required field. ',
            $message
        );
    }

    public function testConvertErrorToMessage()
    {
        $error = [
            'code'      => InputException::INVALID_FIELD_RANGE,
            'fieldName' => 'someFieldName',
            'value'     => 'someFieldValue',
            'minValue'  => 'someMetaData',
        ];
        $converter = new InputExceptionConverter();

        $message = $converter->convertErrorToMessage($error);

        $this->assertEquals(
            'Invalid value of "someFieldValue" provided for someFieldName field.',
            $message
        );
    }
}
