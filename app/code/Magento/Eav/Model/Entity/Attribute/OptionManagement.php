<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Eav\Model\Entity\Attribute;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;

class OptionManagement extends \Magento\Framework\Model\AbstractModel
    implements \Magento\Eav\Api\AttributeOptionManagementInterface
{
    /**
     * @var \Magento\Eav\Model\Entity\AttributeFactory
     */
    protected $attributeFactory;

    /**
     * @param \Magento\Eav\Model\Entity\AttributeFactory $attributeFactory
     */
    public function __construct(
        \Magento\Eav\Model\Entity\AttributeFactory $attributeFactory
    ) {
        $this->attributeFactory = $attributeFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function add($attributeCode, $entityType, $option)
    {
        $attribute = $this->loadAttribute($entityType, $attributeCode);
        if (!$attribute->usesSource()) {
            throw new StateException(sprintf('Attribute %s doesn\'t work with options', $attributeCode));
        }
        $key = 'new_option';

        $options = [];
        $options['value'][$key][0] = $option->getLabel();
        $options['order'][$key] = $option->getSortOrder();

        if (is_array($option->getStoreLabels())) {
            foreach ($option->getStoreLabels() as $label) {
                $options['value'][$key][$label->getStoreId()] = $label->getLabel();
            }
        }

        if ($option->getIsDefault()) {
            $attribute->setDefault([$key]);
        }

        $attribute->setOption($options);
        try {
            $attribute->save();
        } catch (\Exception $e) {
            throw new StateException(sprintf('Cannot save attribute %s', $attributeCode));
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($entityType, $attributeCode, $option)
    {
        $attribute = $this->loadAttribute($entityType, $attributeCode);
        if (!$attribute->usesSource()) {
            throw new StateException(sprintf('Attribute %s doesn\'t have any option', $attributeCode));
        }

        $optionId = $option->getId();
        if (!$attribute->getSource()->getOptionText($optionId)) {
            throw new NoSuchEntityException(
                sprintf('Attribute %s does not contain option with Id %s', $attribute->getId(), $optionId)
            );
        }

        $removalMarker = [
            'option' => [
                'value' => [$optionId => []],
                'delete' => [$optionId => '1']
            ]
        ];
        $attribute->addData($removalMarker);
        try {
            $attribute->save();
        } catch (\Exception $e) {
            throw new StateException(sprintf('Cannot save attribute %s', $attributeCode));
        }

        return true;

    }

    /**
     * {@inheritdoc}
     */
    public function getItems($entityType, $attributeCode)
    {
        $attribute = $this->loadAttribute($entityType, $attributeCode);

        try {
            $options = $attribute->getOptions();
        } catch (\Exception $e) {
            throw new StateException(sprintf('Cannot load options for attribute %s', $attributeCode));
        }

        return $options;
    }

    /**
     * @param string $entityType
     * @param string $attributeCode
     * @return \Magento\Eav\Model\Entity\Attribute
     * @throws NoSuchEntityException
     */
    protected function loadAttribute($entityType, $attributeCode)
    {
        /** @var \Magento\Eav\Model\Entity\Attribute $attribute */
        $attribute = $this->attributeFactory->create();
        try {
            $attribute->loadByCode($entityType, $attributeCode);
        } catch (\Exception $e) {
            throw new NoSuchEntityException(sprintf('Cannot load attribute %s', $attributeCode));
        }

        return $attribute;
    }
}
