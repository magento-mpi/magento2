<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Bundle\Service\V1\Product\Option\Type;

use Magento\Bundle\Model\Source\Option\Type as TypeModel;
use Magento\Bundle\Service\V1\Data\Product\Option\Type;
use Magento\Bundle\Service\V1\Data\Product\Option\TypeConverter;

class ReadService implements ReadServiceInterface
{
    /**
     * @var TypeModel
     */
    private $type;

    /**
     * @var TypeConverter
     */
    private $typeConverter;

    /**
     * @param TypeModel $type
     * @param TypeConverter $typeConverter
     */
    public function __construct(TypeModel $type, TypeConverter $typeConverter)
    {
        $this->type = $type;
        $this->typeConverter = $typeConverter;
    }

    /**
     * {@inheritdoc}
     */
    public function getTypes()
    {
        $optionList = $this->type->toOptionArray();

        /** @var Type[] $typeDtoList */
        $typeDtoList = [];
        foreach ($optionList as $option) {
            $typeDtoList[] = $this->typeConverter->createDataFromModel($option);
        }
        return $typeDtoList;
    }
}
