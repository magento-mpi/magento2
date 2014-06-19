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
            $linkList[] = $this->createLink($link);
        }
        return $linkList;
    }

    /**
     * @param \Magento\Downloadable\Model\Link $linkData
     * @return \Magento\Downloadable\Service\V1\DownloadableLink\Data\DownloadableLinkInfo
     */
    protected function createLink(\Magento\Downloadable\Model\Link $linkData)
    {
        $this->linkBuilder->populateWithArray([]);
        $this->linkBuilder->setId($linkData->getId());
        $this->linkBuilder->setTitle($linkData->getStoreTitle());
        $this->linkBuilder->setPrice($linkData->getPrice());
        $this->linkBuilder->setNumberOfDownloads($linkData->getNumberOfDownloads());
        $this->linkBuilder->setSortOrder($linkData->getSortOrder());
        $this->linkBuilder->setSharable($linkData->getIsShareable());
        $this->linkBuilder->setLinkResource($this->entityInfoGenerator('link', $linkData));
        $this->linkBuilder->setSampleResource($this->entityInfoGenerator('sample', $linkData));
        return $this->linkBuilder->create();
    }

    /**
     * @param string $entityType 'link' or 'sample'
     * @param \Magento\Downloadable\Model\Link $linkData
     * @return \Magento\Downloadable\Service\V1\DownloadableLink\Data\DownloadableResourceInfo|null
     */
    protected function entityInfoGenerator($entityType, \Magento\Downloadable\Model\Link $linkData)
    {
        $type = $linkData->getData($entityType . '_type');
        if (empty($type)) {
            return null;
        }
        $this->resourceBuilder->populateWithArray([]);
        $this->resourceBuilder->setType($type);
        $this->resourceBuilder->setUrl($linkData->getData($entityType . '_url'));
        $this->resourceBuilder->setFile($linkData->getData($entityType . '_file'));
        return $this->resourceBuilder->create();

    }

    /**
     * {@inheritdoc}
     */
    public function samples($productSku)
    {

    }

}