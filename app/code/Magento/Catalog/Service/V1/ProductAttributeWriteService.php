<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Catalog\Service\V1\Data\Eav\AttributeMetadata;

/**
 * Class ProductAttributeWriteService
 * @package Magento\Catalog\Service\V1
 */
class ProductAttributeWriteService implements ProductAttributeWriteServiceInterface
{
    /**
     * @var \Magento\Eav\Model\Config
     */
    private $eavConfig;

    /**
     * @param \Magento\Eav\Model\Config $eavConfig
     */
    public function __construct(
        \Magento\Eav\Model\Config $eavConfig
    ) {
        $this->eavConfig = $eavConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function remove($id)
    {
        $model = $this->eavConfig->getAttribute(ProductMetadataServiceInterface::ENTITY_TYPE_PRODUCT, $id);
        if (!$model) {
            //product attribute does not exist
            throw NoSuchEntityException::singleField(AttributeMetadata::ATTRIBUTE_ID, $id);
        }
        $model->delete();
        return true;
    }
}
