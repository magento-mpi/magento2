<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Search\Adapter\Mysql;

/**
 * Document Factory
 */
class DocumentFactory
{
    /**
     * Object Manager instance
     *
     * @var \Magento\Framework\ObjectManager
     */
    protected $objectManager;
    /**
     * @var \Magento\Framework\Search\EntityMetadata
     */
    private $entityId;

    /**
     * @param \Magento\Framework\ObjectManager $objectManager
     * @param \Magento\Framework\Search\EntityMetadata $entityId
     */
    public function __construct(
        \Magento\Framework\ObjectManager $objectManager,
        \Magento\Framework\Search\EntityMetadata $entityId
    ) {
        $this->objectManager = $objectManager;
        $this->entityId = $entityId;
    }

    /**
     * Create Search Document instance
     *
     * @param mixed $rawDocument
     * @return \Magento\Framework\Search\Document
     */
    public function create($rawDocument)
    {
        /** @var \Magento\Framework\Search\DocumentField[] $fields */
        $fields = [];
        $documentId = null;
        $entityId = $this->entityId->getEntityId();
        foreach ($rawDocument as $rawField) {
            if ($rawField['name'] == $entityId) {
                $documentId = $rawField['value'];
            } else {
                $fields[] = $this->objectManager->create('\Magento\Framework\Search\DocumentField', $rawField);
            }
        }
        return $this->objectManager->create(
            '\Magento\Framework\Search\Document',
            ['documentFields' => $fields, 'documentId' => $documentId]
        );
    }
}
