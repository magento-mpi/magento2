<?php
/**
 * Bool Query
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Search\Request\Filter;

use Magento\Framework\Search\Request\QueryInterface;

class Bool implements QueryInterface
{
    /**
     * Query Name
     *
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
     * Query names to which result set MUST satisfy
     *
     * @var array
     */
    protected $must = array();

    /**
     * Query names to which result set SHOULD satisfy
     *
     * @var array
     */
    protected $should = array();

    /**
     * Query names to which result set MUST NOT satisfy
     *
     * @var array
     */
    protected $mustNot = array();

    /**
     * @param string $name
     * @param int|null $boost
     * @param array $must
     * @param array $should
     * @param array $mustNot
     */
    public function __construct($name, $boost, array $must, array $should, array $mustNot)
    {
        $this->name = $name;
        $this->boost = $boost;
        $this->must = $must;
        $this->should = $should;
        $this->mustNot = $mustNot;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return QueryInterface::TYPE_BOOL;
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
     * Get Must queries
     *
     * @return array
     */
    public function getMust()
    {
        return $this->must;
    }

    /**
     * Get Should queries
     *
     * @return array
     */
    public function getShould()
    {
        return $this->should;
    }

    /**
     * Get Must Not queries
     *
     * @return array
     */
    public function getMustNot()
    {
        return $this->mustNot;
    }
}
