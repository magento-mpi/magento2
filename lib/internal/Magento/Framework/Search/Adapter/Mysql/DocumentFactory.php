<?php
/**
 * Response Factory
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Search\Adapter\Mysql;

class DocumentFactory
{
    /**
     * Object Manager instance
     *
     * @var \Magento\Framework\ObjectManager
     */
    protected $objectManager;

    /**
     * Field Factory
     *
     * @param \Magento\Framework\Search\DocumentFieldFactory $fieldFactory
     */
    protected $fieldFactory;

    /**
     * @param \Magento\Framework\ObjectManager $objectManager
     * @param \Magento\Framework\Search\DocumentFieldFactory $fieldFactory
     */
    public function __construct(
        \Magento\Framework\ObjectManager $objectManager,
        \Magento\Framework\Search\DocumentFieldFactory $fieldFactory
    ) {
        $this->objectManager = $objectManager;
        $this->fieldFactory = $fieldFactory;
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
        foreach($rawDocument as $rawField) {
            /** @var \Magento\Framework\Search\DocumentField[] $fields */
            $fields[] = $this->objectManager->create(
                '\Magento\Framework\Search\DocumentField',
                [
                    $rawField['name'],
                    $rawField['values'],
                    $rawField['boost'],
                ]
            );
        }
        return $this->objectManager->create('\Magento\Framework\Search\Document', $fields);
    }
}
