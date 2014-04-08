<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Exception;

class NoSuchEntityExceptionTest extends \PHPUnit_Framework_TestCase
{

    public function testConstructor()
    {
        $exception = new NoSuchEntityException();
        $this->assertEquals('No such entity.', $exception->getMessage());
        $this->assertEquals('No such entity.', $exception->getRawMessage());
        $this->assertEquals('No such entity.', $exception->getLogMessage());

        $exception = new NoSuchEntityException(
            'No such entity with %fieldName = %value',
            ['fieldName' => 'field', 'value' => 'value']
        );
        $this->assertEquals('No such entity with field = value', $exception->getMessage());
        $this->assertEquals('No such entity with %fieldName = %value', $exception->getRawMessage());
        $this->assertEquals('No such entity with field = value', $exception->getLogMessage());
        $this->assertEquals(['fieldName' => 'field', 'value' => 'value'], $exception->getParams());

        $exception = new NoSuchEntityException(
            'No such entity with %fieldName1 = %value1, %fieldName2 = %value2',
            [
                'fieldName1' => 'field1',
                'value1' => 'value1',
                'fieldName2' => 'field2',
                'value2' => 'value2'
            ]
        );
        $this->assertEquals('No such entity with field1 = value1, field2 = value2', $exception->getMessage());
        $this->assertEquals(
            'No such entity with %fieldName1 = %value1, %fieldName2 = %value2',
            $exception->getRawMessage()
        );
        $this->assertEquals('No such entity with field1 = value1, field2 = value2', $exception->getLogMessage());
    }
}
