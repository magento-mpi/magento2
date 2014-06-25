<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Product\Attribute;

use Magento\Catalog\Model\Product\Attribute\Source\InputtypeFactory;
use Magento\Catalog\Service\V1\MetadataServiceInterface;
use Magento\Catalog\Service\V1\Product\MetadataServiceInterface as ProductMetadataServiceInterface;
/**
 * Class ProductAttributeService
 */
class OptionService implements OptionServiceInterface
{
    /**
     * @var MetadataServiceInterface
     */
    private $metadataService;

    /**
     * @var \Magento\Eav\Model\Config
     */
    private $eavConfig;

    /** @var  \Magento\Eav\Model\Resource\Entity\Attribute\Option\CollectionFactory */
    private $optionCollectionFactory;

    /**
     * @param MetadataServiceInterface $metadataService
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Eav\Model\Resource\Entity\Attribute\Option\CollectionFactory $optionCollectionFactory
     */
    public function __construct(
        ProductMetadataServiceInterface $metadataService,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Eav\Model\Resource\Entity\Attribute\Option\CollectionFactory $optionCollectionFactory
    ) {
        $this->metadataService = $metadataService;
        $this->eavConfig = $eavConfig;
        $this->optionCollectionFactory = $optionCollectionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function options($id)
    {
        return $this->metadataService->getAttributeMetadata(
            ProductMetadataServiceInterface::ENTITY_TYPE,
            $id
        )->getOptions();
    }

    /**
     * {@inheritdoc}
     */
    public function addOption($id, \Magento\Catalog\Service\V1\Data\Eav\Option $option)
    {
        $model = $this->eavConfig->getAttribute(
            ProductMetadataServiceInterface::ENTITY_TYPE,
            $id
        );
        if (!$model) {
            throw new \Magento\Framework\Exception\StateException('Attribute does no exist');
        }

        if (!$model->usesSource()) {
            throw new \Magento\Framework\Exception\StateException('Attribute doesn\'t have any option');
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
