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
     * @param \Magento\Framework\ObjectManager $objectManager
     */
    public function __construct(
        \Magento\Framework\ObjectManager $objectManager
    ) {
        $this->objectManager = $objectManager;
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
        foreach ($rawDocument as $rawField) {
            /** @var \Magento\Framework\Search\DocumentField[] $fields */
            $fields[] = $this->objectManager->create(
                '\Magento\Framework\Search\DocumentField',
                [
                    $rawField['name'],
                    $rawField['values']
                ]
            );
        }
        return $this->objectManager->create('\Magento\Framework\Search\Document', $fields);
    }
}
