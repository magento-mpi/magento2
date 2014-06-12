<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Install\Tests\Ddl;

class TableTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Install\Ddl\Table
     */
    protected $model;

    public function setUp()
    {
        $this->model = new \Magento\Install\Ddl\Table();
    }

    public function testGetSetEngine()
    {
        $this->assertEquals(\Magento\Install\Ddl\Table::ENGINE_INNODB, $this->model->getEngine());//default value
        $engine = \Magento\Install\Ddl\Table::ENGINE_MYISAM;
        $this->model->setEngine($engine);
        $this->assertEquals($engine, $this->model->getEngine());
    }

    public function testGetSetCharset()
    {
        $this->assertEquals(\Magento\Install\Ddl\Table::CHARSET_UTF8, $this->model->getCharset());//default value
        $charset = 'koi8-r';
        $this->model->setCharset($charset);
        $this->assertEquals($charset, $this->model->getCharset());
    }

    public function testAddGetColumns()
    {
        $this->assertNull($this->model->getColumns());
        $columnMock = $this->getMock('Magento\Install\Ddl\Column', array(), array(), '', false);
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

    public function testGetSetComment()
    {
        $this->assertNull($this->model->getComment());
        $comment = 'some_comment';
        $this->model->setComment($comment);
        $this->assertEquals($comment, $this->model->getComment());
    }
}
