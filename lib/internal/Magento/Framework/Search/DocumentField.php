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
     * Field Boost
     *
     * @var float
     */
    protected $boost;

    /**
     * @param string $name
     * @param array $values
     * @param float $boost
     */
    public function __construct(
        $name,
        array $values,
        $boost
    ) {
        $this->name = $name;
        $this->values = $values;
        $this->boost = $boost;
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

    /**
     * Get field boost
     *
     * @return float
     */
    public function getBoost()
    {
        return $this->boost;
    }
}
