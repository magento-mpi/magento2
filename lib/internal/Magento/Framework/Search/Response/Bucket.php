<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Search\Response;

use Magento\Framework\Search\Response\Aggregation\Value;

/**
 * Facet Bucket
 */
class Bucket
{
    /**
     * Field name
     *
     * @var string
     */
    protected $name;

    /**
     * Field values
     *
     * @var mixed
     */
    protected $values;

    /**
     * @param string $name
     * @param mixed $values
     */
    public function __construct($name, $values)
    {
        $this->name = $name;
        $this->values = $values;
    }

    /**
     * Get field name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get field values
     *
     * @return Value[]
     */
    public function getValues()
    {
        return $this->values;
    }
}
