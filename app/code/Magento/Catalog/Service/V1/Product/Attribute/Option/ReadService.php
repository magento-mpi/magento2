<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Product\Attribute\Option;

use Magento\Catalog\Service\V1\MetadataServiceInterface as MetadataServiceInterface;

class ReadService implements ReadServiceInterface
{
    /**
     * @var MetadataServiceInterface
     */
    private $metadataService;

    /**
     * @param MetadataServiceInterface $metadataService
     */
    public function __construct(
        MetadataServiceInterface $metadataService
    ) {
        $this->metadataService = $metadataService;
    }

    /**
     * {@inheritdoc}
     */
    public function options($id)
    {
        return $this->metadataService->getAttributeMetadata(
            \Magento\Catalog\Service\V1\Product\MetadataServiceInterface::ENTITY_TYPE,
            $id
        )->getOptions();
    }
}
