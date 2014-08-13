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
     * @param Document[] $documents
     */
    public function __construct(array $documents)
    {
        $this->documents = $documents;
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
}
