<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Eav\Model\Entity\Attribute;

class Identifier extends \Magento\Framework\Object implements \Magento\Eav\Api\Data\AttributeIdentifierInterface
{
    /**
     * @var string
     */
    protected $entityTypeCode;

    /**
     * @var string
     */
    protected $attributeCode;

    /**
     * @param $entityTypeCode
     * @param $attributeCode
     */
    public function __construct($entityTypeCode, $attributeCode)
    {
        $this->entityTypeCode = $entityTypeCode;
        $this->attributeCode = $attributeCode;
    }

    /**
     * {@inheritdoc}
     */
    public function getEntityTypeCode()
    {
        return $this->entityTypeCode;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributeCode()
    {
        return $this->attributeCode;
    }
}
