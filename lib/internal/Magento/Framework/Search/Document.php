<?php
/**
 * Search Document
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Search;

class Document implements \ArrayAccess, \IteratorAggregate, \Countable
{
    /**
     * Document fields array
     *
     * @var DocumentField[]
     */
    protected $documentFields;

    /**
     * Document Boost
     *
     * @var float
     */
    protected $documentBoost;

    /**
     * Document Id
     *
     * @var int
     */
    protected $documentId;

    /**
     * @param int $documentId
     * @param float $documentBoost
     * @param DocumentField[] $documentFields
     */
    public function __construct(
        $documentId,
        $documentBoost,
        array $documentFields
    ) {
        $this->documentId = $documentId;
        $this->documentBoost = $documentBoost;
        $this->documentFields = $documentFields;
    }

    /**
     *
     * @param string $fieldName
     * @return array
     */
    public function offsetExists($fieldName)
    {
        return isset($this->documentFields[$fieldName]);
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param string $fieldName
     * @param DocumentField $field
     * @throws \BadMethodCallException
     */
    public function offsetSet($fieldName, $field)
    {
        throw new \BadMethodCallException('Search document is read-only.');
    }

    /**
     * @param string $fieldName
     * @return DocumentField
     */
    public function offsetGet($fieldName)
    {
        return $this->documentFields[$fieldName];
    }

    /**
     * @param string $fieldName
     * @throws \BadMethodCallException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function offsetUnset($fieldName)
    {
        throw new \BadMethodCallException('Search document is read-only.');
    }

    /**
     * Countable: return count of fields in document
     * @return int
     */
    public function count()
    {
        return count($this->documentFields);
    }

    /**
     * Implementation of \IteratorAggregate::getIterator()
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->documentFields);
    }

    /**
     * Get Document field
     *
     * @param string $fieldName
     * @return DocumentField
     */
    public function getField($fieldName)
    {
        return $this->documentFields[$fieldName];
    }

    /**
     * Get Document field names
     *
     * @return array
     */
    public function getFieldNames()
    {
        return array_keys($this->documentFields);
    }

    /**
     * Get Document Id
     *
     * @return int
     */
    public function getId()
    {
        return $this->documentId;
    }
}
