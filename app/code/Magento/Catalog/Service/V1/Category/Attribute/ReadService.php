<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Category\Attribute;

use Magento\Catalog\Model\Product\Attribute\Source\InputtypeFactory;
use Magento\Catalog\Service\V1\ProductMetadataServiceInterface;

/**
 * Class ReadService
 *
 * @package Magento\Catalog\Service\V1\Product\Attribute
 */
class ReadService implements ReadServiceInterface
{
    /**
     * @var \Magento\Catalog\Service\V1\MetadataServiceInterface
     */
    private $metadataService;

    /**
     * @param \Magento\Catalog\Service\V1\MetadataServiceInterface $metadataService

     */
    public function __construct(
        \Magento\Catalog\Service\V1\MetadataServiceInterface $metadataService
    ) {
        $this->metadataService = $metadataService;
    }

    /**
     * {@inheritdoc}
     */
    public function options($id)
    {
        return $this->metadataService->getAttributeMetadata(
            ProductMetadataServiceInterface::ENTITY_TYPE_PRODUCT,
            $id
        )->getOptions();
    }

    /**
     * {@inheritdoc}
     */
    public function info($id)
    {
        return $this->metadataService->getAttributeMetadata(
            ProductMetadataServiceInterface::ENTITY_TYPE_PRODUCT,
            $id
        );
    }

    /**
     * {@inheritdoc}
     */
    public function search(\Magento\Framework\Service\V1\Data\SearchCriteria $searchCriteria)
    {
        return $this->metadataService->getAllAttributeMetadata(
            ProductMetadataServiceInterface::ENTITY_TYPE_PRODUCT,
            $searchCriteria
        );
    }
}
