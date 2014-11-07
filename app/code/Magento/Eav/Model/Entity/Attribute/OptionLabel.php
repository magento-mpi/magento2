<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Eav\Model\Entity\Attribute;

use \Magento\Eav\Api\Data\AttributeOptionLabelInterface;
use Magento\Framework\Model\AbstractExtensibleModel;

/**
 * Entity attribute option label model
 *
 */
class OptionLabel extends AbstractExtensibleModel implements AttributeOptionLabelInterface
{
    /**
     * {@inheritdoc}
     */
    public function getLabel()
    {
        return $this->getData(AttributeOptionLabelInterface::LABEL);
    }

    /**
     * {@inheritdoc}
     */
    public function getStoreId()
    {
        return $this->getData(AttributeOptionLabelInterface::STORE_ID);
    }
}
