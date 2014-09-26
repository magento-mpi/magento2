<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Category\Attribute;

use Magento\Catalog\Model\Product\Attribute\Source\InputtypeFactory;
use Magento\Catalog\Service\V1\Category\MetadataServiceInterface;

/**
 * Class ReadService
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
            MetadataServiceInterface::ENTITY_TYPE,
            $id
        )->getOptions();
    }

    /**
     * {@inheritdoc}
     */
    public function info($id)
    {
        return $this->metadataService->getAttributeMetadata(
            MetadataServiceInterface::ENTITY_TYPE,
            $id
        );
    }

    /**
     * {@inheritdoc}
     */
    public function search(\Magento\Framework\Service\V1\Data\SearchCriteria $searchCriteria)
    {
        return $this->metadataService->getAllAttributeMetadata(
            MetadataServiceInterface::ENTITY_TYPE,
            $searchCriteria
        );
    }
}
