<?php
/**
 * Term Filter
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Search\Request\Filter;

use Magento\Framework\Search\Request\FilterInterface;

class Term implements FilterInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $field;

    /**
     * @var string
     */
    protected $value;

    /**
     * @var string
     */
    protected $operator;

    /**
     * @param string $name
     * @param string $field
     * @param string $value
     * @param string $operator
     */
    public function __construct($name, $field, $value, $operator = FilterInterface::OPERATOR_EQ)
    {
        $this->name = $name;
        $this->field = $field;
        $this->value = $value;
        $this->operator = $operator;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return FilterInterface::TYPE_TERM;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get Field
     *
     * @return string
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * Get Value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Get Operator
     *
     * @return string
     */
    public function getOperator()
    {
        return $this->operator;
    }
}
