<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Search;

/**
 * Facet Bucket
 */
class Bucket
{
    /**
     * Bucket name
     *
     * @var string
     */
    protected $name;

    /**
     * Bucket value
     *
     * @var mixed
     */
    protected $value;

    /**
     * @param string $name
     * @param mixed $value
     */
    public function __construct($name, $value)
    {
        $this->name = $name;
        $this->value = $value;
    }

    /**
     * Get bucket name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get bucket values
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
}
