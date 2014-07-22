<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Bundle\Service\V1\Data\Option;

use Magento\Bundle\Model\Option;

/**
 * @codeCoverageIgnore
 */
class MetadataConverter
{
    /**
     * @var MetadataBuilder
     */
    private $builder;

    /**
     * @param MetadataBuilder $builder
     */
    public function __construct(MetadataBuilder $builder)
    {
        $this->builder = $builder;
    }

    /**
     * @param Option $option
     * @return Metadata
     */
    public function createDataFromModel(Option $option)
    {
        $this->builder->populateWithArray($option->getData())
            ->setId($option->getId())
            ->setTitle($option->getDefaultTitle());
        return $this->builder->create();
    }
}
