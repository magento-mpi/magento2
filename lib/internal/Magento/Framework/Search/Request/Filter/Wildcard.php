<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Search\Request\Filter;

use Magento\Framework\Search\AbstractKeyValuePair;
use Magento\Framework\Search\Request\FilterInterface;

/**
 * Wildcard Filter
 */
class Wildcard extends AbstractKeyValuePair implements FilterInterface
{
    /**
     * @var string
     */
    protected $field;

    /**
     * @param string $name
     * @param string $field
     * @param string|array $value
     */
    public function __construct($name, $field, $value)
    {
        parent::__construct($name, $value);
        $this->field = $field;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return FilterInterface::TYPE_WILDCARD;
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
}
