<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Product\Attribute\Option;

use Magento\Catalog\Service\V1\ProductMetadataServiceInterface;
use Magento\Eav\Model\Config;
use Magento\Framework\Exception\StateException;
use Magento\Catalog\Service\V1\Data\Eav\Option as EavOption;

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
     * @throws StateException
     */
    public function addOption($id, EavOption $option)
    {
        $model = $this->eavConfig->getAttribute(
            ProductMetadataServiceInterface::ENTITY_TYPE_PRODUCT,
            $id
        );
        if (!$model) {
            throw new StateException('Attribute does no exist');
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
} 