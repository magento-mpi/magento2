<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Downloadable\Service\V1\DownloadableLink;

class ReadService implements ReadServiceInterface
{
    /**
     * @var \Magento\Catalog\Model\ProductRepository
     */
    protected $productRepository;

    /**
     * @var \Magento\Downloadable\Model\Product\Type
     */
    protected $downloadableType;

    /**
     * @var \Magento\Downloadable\Service\V1\DownloadableLink\Data\DownloadableLinkInfoBuilder
     */
    protected $linkBuilder;

    /**
     * @var \Magento\Downloadable\Service\V1\DownloadableLink\Data\DownloadableSampleInfoBuilder
     */
    protected $sampleBuilder;

    /**
     * @var \Magento\Downloadable\Service\V1\DownloadableLink\Data\DownloadableResourceInfoBuilder
     */
    protected $resourceBuilder;

    /**
     * @param \Magento\Catalog\Model\ProductRepository $productRepository
     * @param \Magento\Downloadable\Model\Product\Type $downloadableType
     * @param Data\DownloadableLinkInfoBuilder $linkBuilder
     * @param Data\DownloadableSampleInfoBuilder $sampleBuilder
     * @param Data\DownloadableResourceInfoBuilder $resourceBuilder
     */
    public function __construct(
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Downloadable\Model\Product\Type $downloadableType,
        \Magento\Downloadable\Service\V1\DownloadableLink\Data\DownloadableLinkInfoBuilder $linkBuilder,
        \Magento\Downloadable\Service\V1\DownloadableLink\Data\DownloadableSampleInfoBuilder $sampleBuilder,
        \Magento\Downloadable\Service\V1\DownloadableLink\Data\DownloadableResourceInfoBuilder $resourceBuilder
    ) {
        $this->productRepository = $productRepository;
        $this->downloadableType = $downloadableType;
        $this->linkBuilder = $linkBuilder;
        $this->sampleBuilder = $sampleBuilder;
        $this->resourceBuilder = $resourceBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function getList($productSku)
    {
        $linkList = [];
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $this->productRepository->get($productSku);
        $links = $this->downloadableType->getLinks($product);
        /** @var \Magento\Downloadable\Model\Link $link */
        foreach ($links as $link) {
            $linkList[] = $this->buildResource($link);
        }
        return $linkList;
    }

    /**
     * Build a link data object
     *
     * @param \Magento\Downloadable\Model\Link|\Magento\Downloadable\Model\Sample $resourceData
     * @return \Magento\Downloadable\Service\V1\DownloadableLink\Data\DownloadableLinkInfo
     */
    protected function buildResource($resourceData)
    {
        $this->linkBuilder->populateWithArray([]);
        $this->linkBuilder->setId($resourceData->getId());
        $this->linkBuilder->setTitle($resourceData->getStoreTitle());
        $this->linkBuilder->setPrice($resourceData->getPrice());
        $this->linkBuilder->setNumberOfDownloads($resourceData->getNumberOfDownloads());
        $this->linkBuilder->setSortOrder($resourceData->getSortOrder());
        $this->linkBuilder->setSharable($resourceData->getIsShareable());
        $this->linkBuilder->setLinkResource($this->entityInfoGenerator('link', $resourceData));
        $this->linkBuilder->setSampleResource($this->entityInfoGenerator('sample', $resourceData));
        return $this->linkBuilder->create();
    }

    /**
     * Build file info data object
     *
     * @param string $entityType 'link' or 'sample'
     * @param \Magento\Downloadable\Model\Link|\Magento\Downloadable\Model\Sample $resourceData
     * @return \Magento\Downloadable\Service\V1\DownloadableLink\Data\DownloadableResourceInfo|null
     */
    protected function entityInfoGenerator($entityType, $resourceData)
    {
        $type = $resourceData->getData($entityType . '_type');
        if (empty($type)) {
            return null;
        }
        $this->resourceBuilder->populateWithArray([]);
        $this->resourceBuilder->setType($type);
        $this->resourceBuilder->setUrl($resourceData->getData($entityType . '_url'));
        $this->resourceBuilder->setFile($resourceData->getData($entityType . '_file'));
        return $this->resourceBuilder->create();

    }

    /**
     * {@inheritdoc}
     */
    public function samples($productSku)
    {
        $sampleList = [];
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $this->productRepository->get($productSku);
        $samples = $this->downloadableType->getSamples($product);
        /** @var \Magento\Downloadable\Model\Sample $sample */
        foreach ($samples as $sample) {
            $sampleList[] = $this->buildResource($sample);
        }
        return $sampleList;
    }

}