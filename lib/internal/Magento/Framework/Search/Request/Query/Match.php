<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Search\Request\Query;

use Magento\Framework\Search\Request\QueryInterface;

/**
 * Match Query
 */
class Match implements QueryInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * Boost
     *
     * @var int|null
     */
    protected $boost;

    /**
     * Match query array
     * Possible structure:
     * array(
     *     ['field' => 'some_field', 'value' => 'some_value', 'boost' => 'some_boost'],
     *     ['field' => 'some_field', 'value' => 'some_value', 'boost' => 'some_boost'],
     * )
     *
     * @var array
     */
    protected $matches = array();

    /**
     * @param string $name
     * @param int|null $boost
     * @param array $matches
     */
    public function __construct($name, $boost, array $matches)
    {
        $this->name = $name;
        $this->boost = $boost;
        $this->matches = $matches;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return QueryInterface::TYPE_MATCH;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getBoost()
    {
        return $this->boost;
    }

    /**
     * Get Matches
     *
     * @return array
     */
    public function getMatches()
    {
        return $this->matches;
    }
}
