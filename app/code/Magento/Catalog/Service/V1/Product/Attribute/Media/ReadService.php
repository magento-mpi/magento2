<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1\Product\Attribute\Media;

use Magento\Catalog\Service\V1\Product\Attribute\Media\Data\MediaImageBuilder;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\StateException;

class ReadService implements ReadServiceInterface
{
    /**
     * @var \Magento\Catalog\Model\Resource\Product\Attribute\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \Magento\Eav\Model\Entity\Attribute\SetFactory
     */
    protected $setFactory;

    /** @var MediaImageBuilder */
    protected $builder;

    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $eavConfig;

    /**
     * @param \Magento\Catalog\Model\Resource\Product\Attribute\CollectionFactory $collectionFactory
     * @param \Magento\Eav\Model\Entity\Attribute\SetFactory $setFactory
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param MediaImageBuilder $builder
     */
    public function __construct(
        \Magento\Catalog\Model\Resource\Product\Attribute\CollectionFactory $collectionFactory,
        \Magento\Eav\Model\Entity\Attribute\SetFactory $setFactory,
        \Magento\Eav\Model\Config $eavConfig,
        MediaImageBuilder $builder
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->setFactory = $setFactory;
        $this->eavConfig = $eavConfig;
        $this->builder = $builder;
    }

    /**
     * @param \Magento\Catalog\Model\Resource\Eav\Attribute[] $items
     * @return array|\Magento\Catalog\Model\Resource\Eav\Attribute[]
     * @throws \Magento\Framework\Exception\StateException
     */
    protected function prepareData($items)
    {
        $data = [];
        /** @var \Magento\Catalog\Model\Resource\Eav\Attribute $attribute */
        foreach($items as $attribute) {
            $this->builder->setFrontendLabel($attribute->getFrontendLabel());
            $this->builder->setCode($attribute->getData('attribute_code'));
            $this->builder->setIsUserDefined($attribute->getData('is_user_defined'));
            switch(true) {
                case $attribute->getIsGlobal():
                    $scope = 'Global';
                    break;
                case $attribute->isScopeWebsite():
                    $scope = 'Website';
                    break;
                case $attribute->isScopeStore():
                    $scope = 'Store View';
                    break;
                default:
                    throw new StateException('Attribute has invalid scope. Id = ' . $attribute->getId());
                    break;
            }
            $this->builder->setScope($scope);
            $data[] = $this->builder->create();
        }
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function getTypes($attributeSetId)
    {
        $attributeSet = $this->setFactory->create()->load($attributeSetId);
        if (!$attributeSet->getId()) {
            throw NoSuchEntityException::singleField('attribute_set_id', $attributeSetId);
        }

        $productEntityId = $this->eavConfig->getEntityType(\Magento\Catalog\Model\Product::ENTITY)->getId();
        if ($attributeSet->getEntityTypeId() != $productEntityId) {
            throw InputException::invalidFieldValue('entity_type_id', $attributeSetId);
        }

        $collection = $this->collectionFactory->create();
        $collection->setAttributeSetFilter($attributeSetId);
        $collection->setFrontendInputTypeFilter('media_image');

        return $this->prepareData($collection->getItems());
    }
}