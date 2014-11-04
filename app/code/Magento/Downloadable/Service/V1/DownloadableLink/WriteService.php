<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Downloadable\Service\V1\DownloadableLink;

use \Magento\Downloadable\Service\V1\Data\FileContentUploaderInterface;
use \Magento\Downloadable\Service\V1\DownloadableLink\Data\DownloadableLinkContent;
use \Magento\Catalog\Model\ProductRepository;
use \Magento\Downloadable\Service\V1\DownloadableLink\Data\DownloadableLinkContentValidator;
use \Magento\Downloadable\Model\Link;
use \Magento\Framework\Exception\InputException;
use \Magento\Framework\Json\EncoderInterface;
use \Magento\Downloadable\Model\LinkFactory;
use \Magento\Framework\Exception\NoSuchEntityException;

class WriteService implements WriteServiceInterface
{
    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * @var DownloadableLinkContentValidator
     */
    protected $linkContentValidator;

    /**
     * @var FileContentUploaderInterface
     */
    protected $fileContentUploader;

    /**
     * @var EncoderInterface
     */
    protected $jsonEncoder;

    /**
     * @var \Magento\Downloadable\Model\LinkFactory
     */
    protected $linkFactory;

    /**
     * @param ProductRepository $productRepository
     * @param DownloadableLinkContentValidator $linkContentValidator
     * @param FileContentUploaderInterface $fileContentUploader
     * @param EncoderInterface $jsonEncoder
     * @param LinkFactory $linkFactory
     */
    public function __construct(
        ProductRepository $productRepository,
        DownloadableLinkContentValidator $linkContentValidator,
        FileContentUploaderInterface $fileContentUploader,
        EncoderInterface $jsonEncoder,
        LinkFactory $linkFactory
    ) {
        $this->productRepository = $productRepository;
        $this->linkContentValidator = $linkContentValidator;
        $this->fileContentUploader = $fileContentUploader;
        $this->jsonEncoder = $jsonEncoder;
        $this->linkFactory = $linkFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function create($productSku, DownloadableLinkContent $linkContent, $isGlobalScopeContent = false)
    {
        $product = $this->productRepository->get($productSku, true);
        if ($product->getTypeId() !== \Magento\Downloadable\Model\Product\Type::TYPE_DOWNLOADABLE) {
            throw new InputException('Product type of the product must be \'downloadable\'.');
        }
        if (!$this->linkContentValidator->isValid($linkContent)) {
            throw new InputException('Provided link information is invalid.');
        }

        if (!in_array($linkContent->getLinkType(), array('url', 'file'))) {
            throw new InputException('Invalid link type.');
        }
        $title = $linkContent->getTitle();
        if (empty($title)) {
            throw new InputException('Link title cannot be empty.');
        }

        $linkData = array(
            'link_id' => 0,
            'is_delete' => 0,
            'type' => $linkContent->getLinkType(),
            'sort_order' => $linkContent->getSortOrder(),
            'title' => $linkContent->getTitle(),
            'price' => $linkContent->getPrice(),
            'number_of_downloads' => $linkContent->getNumberOfDownloads(),
            'is_shareable' => $linkContent->isShareable()
        );

        if ($linkContent->getLinkType() == 'file') {
            $linkData['file'] = $this->jsonEncoder->encode(array(
                $this->fileContentUploader->upload($linkContent->getLinkFile(), 'link_file')
            ));
        } else {
            $linkData['link_url'] = $linkContent->getLinkUrl();
        }

        if ($linkContent->getSampleType() == 'file') {
            $linkData['sample']['type'] = 'file';
            $linkData['sample']['file'] = $this->jsonEncoder->encode(array(
                $this->fileContentUploader->upload($linkContent->getSampleFile(), 'link_sample_file')
            ));
        } elseif ($linkContent->getSampleType() == 'url') {
            $linkData['sample']['type'] = 'url';
            $linkData['sample']['url'] = $linkContent->getSampleUrl();
        }

        $downloadableData = array('link' => array($linkData));
        $product->setDownloadableData($downloadableData);
        if ($isGlobalScopeContent) {
            $product->setStoreId(0);
        }
        $product->save();
        return $product->getLastAddedLinkId();
    }

    /**
     * {@inheritdoc}
     */
    public function update($productSku, $linkId, DownloadableLinkContent $linkContent, $isGlobalScopeContent = false)
    {
        $product = $this->productRepository->get($productSku, true);
        /** @var $link \Magento\Downloadable\Model\Link */
        $link = $this->linkFactory->create()->load($linkId);
        if (!$link->getId()) {
            throw new NoSuchEntityException('There is no downloadable link with provided ID.');
        }
        if ($link->getProductId() != $product->getId()) {
            throw new InputException('Provided downloadable link is not related to given product.');
        }
        if (!$this->linkContentValidator->isValid($linkContent)) {
            throw new InputException('Provided link information is invalid.');
        }
        if ($isGlobalScopeContent) {
            $product->setStoreId(0);
        }
        $title = $linkContent->getTitle();
        if (empty($title)) {
            if ($isGlobalScopeContent) {
                throw new InputException('Link title cannot be empty.');
            }
            // use title from GLOBAL scope
            $link->setTitle(null);
        } else {
            $link->setTitle($linkContent->getTitle());
        }

        $link->setProductId($product->getId())
            ->setStoreId($product->getStoreId())
            ->setWebsiteId($product->getStore()->getWebsiteId())
            ->setProductWebsiteIds($product->getWebsiteIds())
            ->setSortOrder($linkContent->getSortOrder())
            ->setPrice($linkContent->getPrice())
            ->setIsShareable($linkContent->isShareable())
            ->setNumberOfDownloads($linkContent->getNumberOfDownloads())
            ->save();
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($linkId)
    {
        /** @var $link \Magento\Downloadable\Model\Link */
        $link = $this->linkFactory->create()->load($linkId);
        if (!$link->getId()) {
            throw new NoSuchEntityException('There is no downloadable link with provided ID.');
        }
        $link->delete();
        return true;
    }
}
