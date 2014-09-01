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
     * @var \Magento\Framework\Search\MetadataEntityId
     */
    private $entityId;

    /**
     * @param \Magento\Framework\ObjectManager $objectManager
     * @param \Magento\Framework\Search\MetadataEntityId $entityId
     */
    public function __construct(
        \Magento\Framework\ObjectManager $objectManager,
        \Magento\Framework\Search\MetadataEntityId $entityId
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
        $fields = array();
        $documentId = null;
        if (!empty($rawDocument)) {
            $entityId = $this->entityId->getEntityId();
            $documentId = $rawDocument[$entityId];
            unset($rawDocument[$entityId]);
        }
        foreach ($rawDocument as $rawKey => $rawField) {
            /** @var \Magento\Framework\Search\DocumentField[] $fields */
            $fields[] = $this->objectManager->create(
                '\Magento\Framework\Search\DocumentField',
                [
                    'name' => $rawKey,
                    'value' => $rawField
                ]
            );
        }
        return $this->objectManager->create(
            '\Magento\Framework\Search\Document',
            ['documentFields' => $fields, 'documentId' => $documentId]
        );
    }
}
