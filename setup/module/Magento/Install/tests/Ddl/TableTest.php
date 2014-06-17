<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Install\Tests\Ddl;

use Magento\Install\Ddl\Table;

class TableTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Table
     */
    protected $model;

    public function setUp()
    {
        $this->model = new Table();
    }

    public function testGetSetEngine()
    {
        $this->assertEquals(Table::ENGINE_INNODB, $this->model->getEngine());//default value
        $engine = Table::ENGINE_MYISAM;
        $this->model->setEngine($engine);
        $this->assertEquals($engine, $this->model->getEngine());
    }

    public function testGetSetCharset()
    {
        $this->assertEquals(Table::CHARSET_UTF8, $this->model->getCharset());//default value
        $charset = 'koi8-r';
        $this->model->setCharset($charset);
        $this->assertEquals($charset, $this->model->getCharset());
    }

    public function testAddGetColumns()
    {
        $this->assertNull($this->model->getColumns());
        $columnMock = $this->getMock('Magento\Install\Ddl\Column', array(), array(), '', false);
        /** @var $columnMock \Magento\Install\Ddl\Column */
        $this->model->addColumn($columnMock);
        $this->assertEquals(1, count($this->model->getColumns()));
        $testColumn = array_shift($this->model->getColumns());
        $this->assertInstanceOf('Magento\Install\Ddl\Column', $testColumn);
    }

    public function testGetSetColumns()
    {
        $this->assertNull($this->model->getColumns());
        $columnMock = $this->getMock('Magento\Install\Ddl\Column', array(), array(), '', false);
        $columns = array_fill(0, 3, $columnMock);
        $this->model->setColumns($columns);
        $this->assertEquals(3, count($this->model->getColumns()));
        $testColumn = array_shift($this->model->getColumns());
        $this->assertInstanceOf('Magento\Install\Ddl\Column', $testColumn);
    }

    public function testAddGetIndexes()
    {
        $this->assertNull($this->model->getIndexes());
        $indexMock = $this->getMock('Magento\Install\Ddl\Index', array(), array(), '', false);
        /** @var $indexMock \Magento\Install\Ddl\Index */
        $this->model->addIndex($indexMock);
        $this->assertEquals(1, count($this->model->getIndexes()));
        $testIndex = array_shift($this->model->getIndexes());
        $this->assertInstanceOf('Magento\Install\Ddl\Index', $testIndex);
    }

    public function testGetSetIndexes()
    {
        $this->assertNull($this->model->getIndexes());
        $indexMock = $this->getMock('Magento\Install\Ddl\Index', array(), array(), '', false);
        $indexes = array_fill(0, 3, $indexMock);
        $this->model->setIndexes($indexes);
        $this->assertEquals(3, count($this->model->getIndexes()));
        $testIndex = array_shift($this->model->getIndexes());
        $this->assertInstanceOf('Magento\Install\Ddl\Index', $testIndex);
    }

    public function testGetSetDescription()
    {
        $this->assertNull($this->model->getDescription());
        $description = 'some_comment';
        $this->model->setDescription($description);
        $this->assertEquals($description, $this->model->getDescription());
    }
}
