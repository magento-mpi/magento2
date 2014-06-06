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
use \Magento\Catalog\Service\V1\Product\Attribute\Media\Data\GalleryEntryBuilder;

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

    /** @var \Magento\Catalog\Service\V1\Product\Attribute\Media\Data\MediaImageBuilder */
    protected $builder;

    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $eavConfig;

    /**
     * @var \Magento\Catalog\Model\ProductRepository
     */
    protected $productRepository;

    /**
     * @var GalleryEntryBuilder
     */
    protected $galleryEntryBuilder;

    /**
     * @param \Magento\Catalog\Model\Resource\Product\Attribute\CollectionFactory $collectionFactory
     * @param \Magento\Eav\Model\Entity\Attribute\SetFactory $setFactory
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param MediaImageBuilder $builder
     * @param \Magento\Catalog\Model\ProductRepository $productRepository
     * @param GalleryEntryBuilder $galleryEntryBuilder
     */
    public function __construct(
        \Magento\Catalog\Model\Resource\Product\Attribute\CollectionFactory $collectionFactory,
        \Magento\Eav\Model\Entity\Attribute\SetFactory $setFactory,
        \Magento\Eav\Model\Config $eavConfig,
        MediaImageBuilder $builder,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        GalleryEntryBuilder $galleryEntryBuilder
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->setFactory = $setFactory;
        $this->eavConfig = $eavConfig;
        $this->builder = $builder;
        $this->productRepository = $productRepository;
        $this->galleryEntryBuilder = $galleryEntryBuilder;
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
            if($attribute->getIsGlobal()) {
                $scope = 'Global';
            } elseif($attribute->isScopeWebsite()) {
                $scope = 'Website';
            } elseif($attribute->isScopeStore()) {
                $scope = 'Store View';
            } else {
                throw new StateException('Attribute has invalid scope. Id = ' . $attribute->getId());
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

    /**
     * {@inheritdoc}
     */
    public function info($productSku, $imageId)
    {
        try {
            $product = $this->productRepository->get($productSku);
        } catch (NoSuchEntityException $exception) {
            throw $exception;
        } catch (\Exception $exception) {
            throw new NoSuchEntityException("Such product doesn't exist");
        }

        $output = null;
        foreach ($product->getMediaGallery('images') as $image) {
            if (intval($image['value_id']) == intval($imageId)) {
                $image['store_id'] = $product->getStoreId();
                $output = $this->galleryEntryBuilder->populateWithArray($image)->create();
                break;
            }
        }

        if (is_null($output)) {
            throw new NoSuchEntityException("Such image doesn't exist");
        }
        return $output;
    }
}
