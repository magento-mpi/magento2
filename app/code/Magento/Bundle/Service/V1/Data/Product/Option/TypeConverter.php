<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Bundle\Service\V1\Data\Product\Option;

/**
 * @codeCoverageIgnore
 */
class TypeConverter
{
    /**
     * @var TypeBuilder
     */
    private $builder;

    /**
     * @param TypeBuilder $builder
     */
    public function __construct(TypeBuilder $builder)
    {
        $this->builder = $builder;
    }

    /**
     * @param array $option
     * @return Type
     */
    public function createDataFromModel(array $option)
    {
        $this->builder->populateWithArray($option)
            ->setCode($option['value']);
        return $this->builder->create();
    }
}
