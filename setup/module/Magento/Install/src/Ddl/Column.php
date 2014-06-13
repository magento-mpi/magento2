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

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @param bool $isRequired
     */
    public function setRequired($isRequired = true)
    {
        $this->required = (bool) $isRequired;
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
     */
    public function setAutoIncrement($autoIncrement = true)
    {
        $this->autoIncrement = (bool) $autoIncrement;
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
     */
    public function setPrimaryKey($primaryKey = true)
    {
        $this->primaryKey = (bool) $primaryKey;
    }

    /**
     * @return boolean
     */
    public function isPrimaryKey()
    {
        return $this->primaryKey;
    }
}
