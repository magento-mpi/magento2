<?php
/**
 * Attribute data validator
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Model\Metadata;

class Validator extends \Magento\Eav\Model\Validator\Attribute\Data
{
    /**
     * @var string
     */
    protected $_entityType;

    /**
     * @param ElementFactory $attrDataFactory
     */
    public function __construct(ElementFactory $attrDataFactory)
    {
        $this->_attrDataFactory = $attrDataFactory;
    }

    /**
     * Validate EAV model attributes with data models
     *
     * @param \Magento\Core\Model\AbstractModel $entity
     * @return bool
     */
    public function isValid($entity)
    {
        $data = array();
        if ($this->_data) {
            $data = $this->_data;
        } elseif ($entity instanceof \Magento\Object) {
            $data = $entity->getData();
        }
        return $this->validateData($data, $this->_attributes, $this->_entityType);
    }

    /**
     * @param array                                                    $data
     * @param \Magento\Customer\Service\V1\Dto\Eav\AttributeMetadata[] $attributes
     * @param string                                                   $entityType
     * @return bool
     */
    public function validateData(array $data, array $attributes, $entityType)
    {
        foreach ($attributes as $attribute) {
            $attributeCode = $attribute->getAttributeCode();
            if (!$attribute->getDataModel() && !$attribute->getFrontendInput()) {
                continue;
            }
            if (!isset($data[$attributeCode])) {
                $data[$attributeCode] = null;
            }
            $dataModel = $this->_attrDataFactory->create($attribute, $data[$attributeCode], $entityType);
            $dataModel->setExtractedData($data);
            $result = $dataModel->validateValue($data[$attributeCode]);
            if (true !== $result) {
                $this->_addErrorMessages($attributeCode, (array)$result);
            }
        }
        return count($this->_messages) == 0;
    }

    /**
     * Set type of the entity
     *
     * @param string $entityType
     * @return void
     */
    public function setEntityType($entityType)
    {
        $this->_entityType = $entityType;
    }
}
