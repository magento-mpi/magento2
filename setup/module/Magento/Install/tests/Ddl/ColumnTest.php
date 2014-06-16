<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Install\Tests\Ddl;

class ColumnTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Install\Ddl\Column
     */
    protected $model;

    public function setUp()
    {
        $this->model = new \Magento\Install\Ddl\Column();
    }


    public function testGetSetName()
    {
        $this->assertNull($this->model->getName());
        $columnName = 'column_name';
        $this->model->setName($columnName);
        $this->assertEquals($columnName, $this->model->getName());
    }

    /**
     * @param mixed $param
     * @param bool $expectedResult
     *
     * @dataProvider getBooleanDataProvider
     */
    public function testSetIsRequired($param, $expectedResult)
    {
        $this->assertFalse($this->model->isRequired());//default value
        $this->model->setRequired($param);

        $this->assertEquals($expectedResult, $this->model->isRequired());
    }

    /**
     * @param mixed $param
     * @param bool $expectedResult
     *
     * @dataProvider getBooleanDataProvider
     */
    public function testSetIsAutoIncrement($param, $expectedResult)
    {
        $this->assertFalse($this->model->isAutoIncrement());//default value
        $this->model->setAutoIncrement($param);

        $this->assertEquals($expectedResult, $this->model->isAutoIncrement());
    }

    /**
     * @param mixed $param
     * @param bool $expectedResult
     *
     * @dataProvider getBooleanDataProvider
     */
    public function testSetIsPrimaryKey($param, $expectedResult)
    {
        $this->assertFalse($this->model->isPrimaryKey());//default value
        $this->model->setPrimaryKey($param);

        $this->assertEquals($expectedResult, $this->model->isPrimaryKey());
    }

    public function getBooleanDataProvider()
    {
        return array(
            array('', false),
            array('foo', true),
            array(1, true),
            array(0, false),
            array(array(), false),
            array('false', true),
            array(true, true),
            array(false, false),
        );
    }

    public function testGetSetType()
    {
        $this->assertNull($this->model->getType());
        $columnType = \Magento\Install\Ddl\Column::TYPE_CHAR;
        $this->model->setType($columnType);
        $this->assertEquals($columnType, $this->model->getType());
    }

    /**
     * @expectedException        UnexpectedValueException
     * @expectedExceptionMessage Unsupported column type
     */
    public function testGetSetTypeWithException()
    {
        $this->assertNull($this->model->getType());
        $columnType = 'UNSUPPORTED_TYPE';
        $this->model->setType($columnType);
    }


}
