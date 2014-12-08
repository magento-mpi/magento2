<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Product\Attribute\Option;

use Magento\Catalog\Service\V1\Data\Eav\AttributeMetadata;
use Magento\Catalog\Service\V1\Data\Eav\Option as EavOption;
use Magento\Catalog\Service\V1\Product\MetadataServiceInterface as ProductMetadataServiceInterface;
use Magento\Eav\Model\Config;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;

class WriteService implements WriteServiceInterface
{
    /**
     * @var Config
     */
    private $eavConfig;

    /**
     * @param Config $eavConfig
     */
    public function __construct(
        Config $eavConfig
    ) {
        $this->eavConfig = $eavConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function addOption($id, EavOption $option)
    {
        $model = $this->eavConfig->getAttribute(ProductMetadataServiceInterface::ENTITY_TYPE, $id);
        if (!$model || !$model->getId()) {
            throw NoSuchEntityException::singleField(AttributeMetadata::ATTRIBUTE_ID, $id);
        }

        if (!$model->usesSource()) {
            throw new StateException('Attribute doesn\'t have any option');
        }

        $key = 'new_option';

        $options = [];
        $options['value'][$key][0] = $option->getLabel();
        $options['order'][$key] = $option->getOrder();

        if (is_array($option->getStoreLabels())) {
            foreach ($option->getStoreLabels() as $label) {
                $options['value'][$key][$label->getStoreId()] = $label->getLabel();
            }
        }

        if ($option->isDefault()) {
            $model->setDefault([$key]);
        }

        $model->setOption($options);
        $model->save();
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function removeOption($id, $optionId)
    {
        $model = $this->eavConfig->getAttribute(ProductMetadataServiceInterface::ENTITY_TYPE, $id);
        if (!$model || !$model->getId()) {
            throw NoSuchEntityException::singleField(AttributeMetadata::ATTRIBUTE_ID, $id);
        }
        if (!$model->usesSource()) {
            throw new StateException('Attribute doesn\'t have any option');
        }
        if (!$model->getSource()->getOptionText($optionId)) {
            throw new NoSuchEntityException(sprintf('Attribute %s does not contain option with Id %s', $id, $optionId));
        }

        $modelData = ['option' => ['value' => [$optionId => []], 'delete' => [$optionId => '1']]];
        $model->addData($modelData);
        try {
            $model->save();
        } catch (\Exception $e) {
            throw new StateException('Unable to remove option');
        }

        return true;
    }
}
