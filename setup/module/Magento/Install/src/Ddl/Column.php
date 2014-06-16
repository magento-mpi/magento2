<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Install\Ddl;

class Column
{
    const TYPE_BIT       = 'BIT';
    const TYPE_TINYINT   = 'TINYINT';
    const TYPE_SMALLINT  = 'SMALLINT';
    const TYPE_INTEGER   = 'INTEGER';
    const TYPE_BIGINT    = 'BIGINT';
    const TYPE_FLOAT     = 'FLOAT';
    const TYPE_REAL      = 'REAL';
    const TYPE_NUMERIC   = 'NUMERIC';
    const TYPE_DECIMAL   = 'DECIMAL';
    const TYPE_CHAR      = 'CHAR';
    const TYPE_VARCHAR   = 'VARCHAR';
    const TYPE_TEXT      = 'TEXT';
    const TYPE_DATE      = 'DATE';
    const TYPE_TIME      = 'TIME';
    const TYPE_TIMESTAMP = 'TIMESTAMP';
    const TYPE_BINARY    = 'BINARY';
    const TYPE_VARBINARY = 'VARBINARY';
    const TYPE_BLOB      = 'BLOB';
    const TYPE_DOUBLE    = 'DOUBLE';

    /**
     * @var array
     */
    protected $validTypes = array(
        self::TYPE_BIT,
        self::TYPE_TINYINT,
        self::TYPE_SMALLINT,
        self::TYPE_INTEGER,
        self::TYPE_BIGINT,
        self::TYPE_FLOAT,
        self::TYPE_REAL,
        self::TYPE_NUMERIC,
        self::TYPE_DECIMAL,
        self::TYPE_CHAR,
        self::TYPE_VARCHAR,
        self::TYPE_TEXT,
        self::TYPE_DATE,
        self::TYPE_TIME,
        self::TYPE_TIMESTAMP,
        self::TYPE_BINARY,
        self::TYPE_VARBINARY,
        self::TYPE_BLOB,
        self::TYPE_DOUBLE,
    );

    /**
     * @var string
     */
    protected $name;

    /**
     * @var bool
     */
    protected $required = false;

    /**
     * @var bool
     */
    protected $autoIncrement = false;

    /**
     * @var bool
     */
    protected $primaryKey = false;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $size;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Column
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @param bool $isRequired
     * @return Column
     */
    public function setRequired($isRequired = true)
    {
        $this->required = (bool) $isRequired;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isRequired()
    {
        return $this->required;
    }

    /**
     * @param boolean $autoIncrement
     * @return Column
     */
    public function setAutoIncrement($autoIncrement = true)
    {
        $this->autoIncrement = (bool) $autoIncrement;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isAutoIncrement()
    {
        return $this->autoIncrement;
    }

    /**
     * @param boolean $primaryKey
     * @return Column
     */
    public function setPrimaryKey($primaryKey = true)
    {
        $this->primaryKey = (bool) $primaryKey;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isPrimaryKey()
    {
        return $this->primaryKey;
    }

    /**
     * @param $type
     * @return Column
     * @throws \UnexpectedValueException
     */
    public function setType($type)
    {
        if (!in_array($type, $this->validTypes)) {
            throw new \UnexpectedValueException('Unsupported column type');
        }

        $this->type = $type;

        return $this;
    }

    public function getType()
    {
        return $this->type;
    }

    /**
     * @param $size
     * @return Column
     */
    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * @return string
     */
    public function getSize()
    {
        return $this->size;
    }
}
