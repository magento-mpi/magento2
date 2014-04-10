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
    /**
     * Verify that the constructor creates a single instance of InputException with the proper
     * message and array of parameters.
     *
     * @return void
     */
    public function testConstructor()
    {
        $params = ['fieldName' => 'quantity', 'value' => -100, 'minValue' => 0];
        $inputException = new InputException(InputException::INVALID_FIELD_MIN_VALUE, $params);

        $this->assertStringMatchesFormat('%s greater than or equal to %s', $inputException->getMessage());
        $this->assertEquals(
            'The quantity value of "-100" must be greater than or equal to 0.',
            $inputException->getLogMessage()
        );
    }

    /**
     * Verify that adding multiple errors works correctly.
     *
     * @return void
     */
    public function testAddError()
    {
        $inputException = new InputException();
        $this->assertStringStartsWith('One or more', $inputException->getMessage());

        $inputException->addError(
            InputException::INVALID_FIELD_MIN_VALUE,
            ['fieldName' => 'weight', 'value' => -100, 'minValue' => 1]
        );
        $this->assertCount(0, $inputException->getErrors());
        $this->assertEquals(
            'The weight value of "-100" must be greater than or equal to 1.',
            $inputException->getLogMessage()
        );

        $inputException->addError(InputException::REQUIRED_FIELD, ['fieldName' => 'name']);
        $this->assertCount(2, $inputException->getErrors());
        $this->assertStringStartsWith('One or more', $inputException->getMessage());
        $this->assertEquals('One or more input exceptions have occurred.', $inputException->getLogMessage());
        $errors = $inputException->getErrors();
        $this->assertCount(2, $errors);
        $this->assertEquals(
            'The weight value of "-100" must be greater than or equal to 1.',
            $errors[0]->getLogMessage()
        );
        $this->assertEquals('name is a required field.', $errors[1]->getLogMessage());
    }
}
