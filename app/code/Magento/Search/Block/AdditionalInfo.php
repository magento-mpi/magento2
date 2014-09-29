<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Search\Block;

use Magento\Framework\View\Element\Template;
use Magento\Search\Model\AdditionalInfoDataProviderInterface;
use Magento\Search\Model\QueryInterface;
use Magento\Search\Model\QueryManagerInterface;

class AdditionalInfo extends Template implements AdditionalInfoInterface
{

    /**
     * @var QueryInterface
     */
    private $query;

    /**
     * @var string
     */
    private $title;

    /**
     * @var AdditionalInfoDataProviderInterface
     */
    private $additionalInfoDataProvider;

    /**
     * @param Template\Context $context
     * @param AdditionalInfoDataProviderInterface $additionalInfoDataProvider
     * @param QueryManagerInterface $queryManager
     * @param string $title
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        AdditionalInfoDataProviderInterface $additionalInfoDataProvider,
        QueryManagerInterface $queryManager,
        $title,
        array $data = array()
    ) {
        $this->additionalInfoDataProvider = $additionalInfoDataProvider;
        $this->query = $queryManager->getQuery();
        $this->title = $title;
        parent::__construct($context, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getAdditionalInfo()
    {
        $queryText = $this->query->getQueryText();
        return $this->additionalInfoDataProvider->getSearchResult($queryText);
    }

    /**
     * {@inheritdoc}
     */
    public function isCountResultsEnabled()
    {
        return $this->additionalInfoDataProvider->isCountResultsEnabled();
    }

    /**
     * {@inheritdoc}
     */
    public function getLink($queryText)
    {
        return $this->getUrl('*/*/') . '?q=' . urlencode($queryText);
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle()
    {
        return $this->title;
    }
}
