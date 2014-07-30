<?php
/**
 * Bool Filter
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Search\Request\Filter;

use Magento\Framework\Search\Request\FilterInterface;

class Bool implements FilterInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * Filter names to which result set MUST satisfy
     *
     * @var array
     */
    protected $must = array();

    /**
     * Filter names to which result set SHOULD satisfy
     *
     * @var array
     */
    protected $should = array();

    /**
     * Filter names to which result set MUST NOT satisfy
     *
     * @var array
     */
    protected $mustNot = array();

    /**
     * @param string $name
     * @param array $must
     * @param array $should
     * @param array $mustNot
     */
    public function __construct($name, array $must, array $should, array $mustNot)
    {
        $this->name = $name;
        $this->must = $must;
        $this->should = $should;
        $this->mustNot = $mustNot;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return FilterInterface::TYPE_BOOL;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get Must filters
     *
     * @return array
     */
    public function getMust()
    {
        return $this->must;
    }

    /**
     * Get Should filters
     *
     * @return array
     */
    public function getShould()
    {
        return $this->should;
    }

    /**
     * Get Must Not filters
     *
     * @return array
     */
    public function getMustNot()
    {
        return $this->mustNot;
    }
}
