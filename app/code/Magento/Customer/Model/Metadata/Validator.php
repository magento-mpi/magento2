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
     * @param \Magento\Customer\Model\Metadata\ElementFactory $attrDataFactory
     */
    public function __construct(\Magento\Customer\Model\Metadata\ElementFactory $attrDataFactory)
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
        $this->validateData($data, $this->_attributes, $this->_entityType);
    }

    public function validateData($data, $attributes, $entityType)
    {
        foreach ($attributes as $attribute) {
            $attributeCode = $attribute->getAttributeCode();
            if (!$attribute->getDataModel() && !$attribute->getFrontendInput()) {
                continue;
            }
            if (!isset($data[$attributeCode])) {
                $data[$attributeCode] = null;
            }
            $dataModel = $this->_attrDataFactory->create(
                $attribute, $entityType, $data[$attributeCode]
            );
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
     * @return null
     */
    public function setEntityType($entityType)
    {
        $this->_entityType = $entityType;
    }
}
