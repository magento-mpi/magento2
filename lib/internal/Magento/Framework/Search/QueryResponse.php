<?php
/**
 * Search Response
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Search;

class QueryResponse implements ResponseInterface, \IteratorAggregate, \Countable
{
    /**
     * Document Collection
     *
     * @var Document[]
     */
    protected $documents;

    /**
     * Aggregation Collection
     *
     * @var Aggregation[]
     */
    protected $aggregations;

    /**
     * @param Document[] $documents
     * @param Aggregation[] $aggregations
     */
    public function __construct(array $documents, array $aggregations)
    {
        $this->documents = $documents;
        $this->aggregations = $aggregations;
    }

    /**
     * Countable: return count of fields in document
     * @return int
     */
    public function count()
    {
        return count($this->documents);
    }

    /**
     * Implementation of \IteratorAggregate::getIterator()
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->documents);
    }

    /**
     * Return Aggregation Collection
     *
     * @return Aggregation[]
     */
    public function getAggregations()
    {
        return $this->aggregations;
    }
}
