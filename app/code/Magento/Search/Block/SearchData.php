<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Search\Block;

use Magento\Framework\View\Element\Template;
use Magento\Search\Model\SearchDataProviderInterface;
use Magento\Search\Model\QueryInterface;
use Magento\Search\Model\QueryFactoryInterface;

class SearchData extends Template implements SearchDataInterface
{

    /**
     * @var QueryInterface
     */
    private $query;

    /**
     * @var string
     */
    protected $title = '';

    /**
     * @var SearchDataProviderInterface
     */
    private $searchDataProvider;

    /**
     * @var string
     */
    protected $_template = 'search_data.phtml';

    /**
     * @param Template\Context $context
     * @param SearchDataProviderInterface $searchDataProvider
     * @param QueryFactoryInterface $queryFactory
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        SearchDataProviderInterface $searchDataProvider,
        QueryFactoryInterface $queryFactory,
        array $data = array()
    ) {
        $this->searchDataProvider = $searchDataProvider;
        $this->query = $queryFactory->getQuery();
        parent::__construct($context, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchData()
    {
        return $this->searchDataProvider->getSearchData($this->query);
    }

    /**
     * {@inheritdoc}
     */
    public function isCountResultsEnabled()
    {
        return $this->searchDataProvider->isCountResultsEnabled();
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
