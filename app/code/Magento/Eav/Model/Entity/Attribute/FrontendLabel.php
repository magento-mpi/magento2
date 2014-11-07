<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Eav\Model\Entity\Attribute;

class FrontendLabel extends \Magento\Framework\Model\AbstractExtensibleModel implements
    \Magento\Eav\Api\Data\AttributeFrontendLabelInterface
{
    /**
     * {@inheritdoc}
     */
    public function getLabel()
    {
        return $this->getData(self::LABEL);
    }

    /**
     * {@inheritdoc}
     */
    public function getStoreId()
    {
        return $this->getData(self::STORE_ID);
    }
}
