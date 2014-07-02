<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Product\Attribute\Option;

use \Magento\Catalog\Service\V1\Product\MetadataServiceInterface as ProductMetadataServiceInterface;

class ReadService implements ReadServiceInterface
{
    /**
     * @var ProductMetadataServiceInterface
     */
    private $metadataService;

    /**
     * @param ProductMetadataServiceInterface $metadataService
     */
    public function __construct(
        ProductMetadataServiceInterface $metadataService
    ) {
        $this->metadataService = $metadataService;
    }

    /**
     * {@inheritdoc}
     */
    public function options($id)
    {
        return $this->metadataService->getAttributeMetadata(
            ProductMetadataServiceInterface::ENTITY_TYPE,
            $id
        )->getOptions();
    }
}
