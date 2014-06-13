<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Install\Ddl;


class Table
{
    const ENGINE_INNODB             = 'InnoDB';
    const ENGINE_MYISAM             = 'MyISAM';
    const ENGINE_ARCHIVE            = 'ARCHIVE';
    const ENGINE_MEMMORY            = 'MEMMORY';
    const ENGINE_CSV                = 'CSV';
    const ENGINE_MRG_MYISSAM        = 'MRG_MYISSAM';
    const ENGINE_BLACKHOLE          = 'BLACKHOLE';
    const ENGINE_PERFORMANCE_SCHEMA = 'PERFORMANCE_SCHEMA';

    const CHARSET_UTF8 = 'utf-8';

    /**
     * @var Column[]
     */
    protected $columns;

    /**
     * @var Index[]
     */
    protected $indexes;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var string
     */
    protected $charset = self::CHARSET_UTF8;

    /**
     * @var string
     */
    protected $engine = self::ENGINE_INNODB;

    /**
     * @return Column[]
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * @param Column[] $columns
     */
    public function setColumns($columns)
    {
        $this->columns = $columns;
    }

    /**
     * @param Column $column
     */
    public function addColumn(Column $column)
    {
        $this->columns[] = $column;
    }

    /**
     * @return Index[]
     */
    public function getIndexes()
    {
        return $this->indexes;
    }

    /**
     * @param Index[] $indexes
     */
    public function setIndexes($indexes)
    {
        $this->indexes = $indexes;
    }

    /**
     * @param Index $index
     */
    public function addIndex(Index $index)
    {
        $this->indexes[] = $index;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getEngine()
    {
        return $this->engine;
    }

    /**
     * @param string $engine
     */
    public function setEngine($engine)
    {
        $this->engine = $engine;
    }

    /**
     * @return string
     */
    public function getCharset()
    {
        return $this->charset;
    }

    /**
     * @param string $charset
     */
    public function setCharset($charset)
    {
        $this->charset = $charset;
    }
}
