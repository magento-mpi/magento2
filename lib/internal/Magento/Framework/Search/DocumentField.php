<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Search;

/**
 * Search Document Field
 */
class DocumentField
{
    /**
     * Document field name
     *
     * @var string
     */
    protected $name;

    /**
     * Document field values
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
     * @return array
     */
    public function getValue()
    {
        return $this->value;
    }
}
