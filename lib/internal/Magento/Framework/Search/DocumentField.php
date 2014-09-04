<?php
/**
 * Search Document Field
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Search;

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
     * @var array
     */
    protected $values;

    /**
     * @param string $name
     * @param array $values
     */
    public function __construct($name, array $values)
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
     * @return array
     */
    public function getValues()
    {
        return $this->values;
    }
}
