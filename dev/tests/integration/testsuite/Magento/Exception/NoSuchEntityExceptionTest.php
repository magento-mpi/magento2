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
        $this->assertEquals('No such entity', $exception->getMessage());
        $this->assertEquals('No such entity', $exception->getRawMessage());
        $this->assertEquals('No such entity', $exception->getLogMessage());
        $this->assertEquals([], $exception->getParams());

        $exception = new NoSuchEntityException('No such entity with %fieldName = %value', ['fieldName' => 'field', 'value' => 'value']);
        $this->assertEquals('No such entity with field = value', $exception->getMessage());
        $this->assertEquals('No such entity with %fieldName = %value', $exception->getRawMessage());
        $this->assertEquals('No such entity with field = value', $exception->getLogMessage());
        $this->assertEquals(['fieldName' => 'field', 'value' => 'value'], $exception->getParams());
    }

    public function testAddField()
    {
        $exception = new NoSuchEntityException();
        $exception->addField('field1', 'value1');
        $this->assertEquals('No such entity with field1 = value1', $exception->getMessage());
        $this->assertEquals('No such entity with %fieldName0 = %value0', $exception->getRawMessage());
        $this->assertEquals('No such entity with field1 = value1', $exception->getLogMessage());
        $this->assertEquals(['fieldName0' => 'field1', 'value0' => 'value1'], $exception->getParams());

        $exception = new NoSuchEntityException('No such entity with %fieldName = %value', ['fieldName' => 'field', 'value' => 'value']);
        $exception->addField('field1', 'value1');
        $this->assertEquals("No such entity with field = value\n field1 = value1", $exception->getMessage());
        $this->assertEquals("No such entity with %fieldName = %value\n %fieldName1 = %value1", $exception->getRawMessage());
        $this->assertEquals("No such entity with field = value\n field1 = value1", $exception->getLogMessage());
        $this->assertEquals(
            ['fieldName' => 'field', 'value' => 'value', 'fieldName1' => 'field1', 'value1' => 'value1'],
            $exception->getParams());
    }

}
