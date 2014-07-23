<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Bundle\Service\V1\Product\Option\Type;

use Magento\Bundle\Model\Source\Option\Type;
use Magento\Bundle\Service\V1\Data\Option\Type\Metadata;
use Magento\Bundle\Service\V1\Data\Option\Type\MetadataBuilder;

class ReadService implements ReadServiceInterface
{
    /**
     * @var Type
     */
    private $type;

    /**
     * @var MetadataBuilder
     */
    private $metadataBuilder;

    public function __construct(Type $type, MetadataBuilder $metadataBuilder)
    {
        $this->type = $type;
        $this->metadataBuilder = $metadataBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function getTypes()
    {
        $optionList = $this->type->toOptionArray();

        /** @var Metadata[] $typeMetadataList */
        $typeMetadataList = [];
        foreach ($optionList as $option) {
            $typeArray = [
                Metadata::LABEL => __($option['label']),
                Metadata::CODE => $option['value']
            ];
            $typeMetadataList[] = $this->metadataBuilder->populateWithArray($typeArray)->create();
        }
        return $typeMetadataList;
    }
}
