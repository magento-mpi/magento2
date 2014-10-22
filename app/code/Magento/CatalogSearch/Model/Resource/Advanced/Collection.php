<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogSearch\Model\Resource\Advanced;

use Magento\Catalog\Model\Product;

/**
 * Collection Advanced
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Collection extends \Magento\Catalog\Model\Resource\Product\Collection
{
    /**
     * List Of filters
     * @var array
     */
    private $filters = [];

    /**
     * @var \Magento\Framework\Search\Request\Builder
     */
    private $requestBuilder;

    /**
     * @var \Magento\Search\Model\SearchEngine
     */
    private $searchEngine;

    /**
     * @param \Magento\Core\Model\EntityFactory $entityFactory
     * @param \Magento\Framework\Logger $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Framework\App\Resource $resource
     * @param \Magento\Eav\Model\EntityFactory $eavEntityFactory
     * @param \Magento\Catalog\Model\Resource\Helper $resourceHelper
     * @param \Magento\Framework\Validator\UniversalFactory $universalFactory
     * @param \Magento\Framework\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Module\Manager $moduleManager ,
     * @param \Magento\Catalog\Model\Indexer\Product\Flat\State $catalogProductFlatState
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Catalog\Model\Product\OptionFactory $productOptionFactory
     * @param \Magento\Catalog\Model\Resource\Url $catalogUrl
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\Stdlib\DateTime $dateTime
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Magento\Framework\Search\Request\Builder $requestBuilder
     * @param \Magento\Search\Model\SearchEngine $searchEngine
     * @param \Zend_Db_Adapter_Abstract $connection
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Core\Model\EntityFactory $entityFactory,
        \Magento\Framework\Logger $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Framework\App\Resource $resource,
        \Magento\Eav\Model\EntityFactory $eavEntityFactory,
        \Magento\Catalog\Model\Resource\Helper $resourceHelper,
        \Magento\Framework\Validator\UniversalFactory $universalFactory,
        \Magento\Framework\StoreManagerInterface $storeManager,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Catalog\Model\Indexer\Product\Flat\State $catalogProductFlatState,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Catalog\Model\Product\OptionFactory $productOptionFactory,
        \Magento\Catalog\Model\Resource\Url $catalogUrl,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        \Magento\Framework\Search\Request\Builder $requestBuilder,
        \Magento\Search\Model\SearchEngine $searchEngine,
        $connection = null
    ) {
        $this->requestBuilder = $requestBuilder;
        $this->searchEngine = $searchEngine;
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $eavConfig,
            $resource,
            $eavEntityFactory,
            $resourceHelper,
            $universalFactory,
            $storeManager,
            $moduleManager,
            $catalogProductFlatState,
            $scopeConfig,
            $productOptionFactory,
            $catalogUrl,
            $localeDate,
            $customerSession,
            $dateTime,
            $connection
        );
    }

    /**
     * Add not indexable fields to search
     *
     * @param array $fields
     * @return $this
     * @throws \Magento\Framework\Model\Exception
     */
    public function addFieldsToFilter($fields)
    {
        if ($fields) {
            $this->filters = array_merge($this->filters, $fields);
        }
        return $this;
    }

    /**
     * @inheritdoc
     */
    protected function _renderFiltersBefore()
    {
        if ($this->filters) {
            $this->requestBuilder->bindDimension('scope', $this->getStoreId());
            $this->requestBuilder->setRequestName('advanced_search_container');
            foreach ($this->filters as $attributes) {
                foreach ($attributes as $attributeCode => $attributeValue) {
                    if (is_numeric($attributeCode)) {
                        $attributeCode = $this->_eavConfig->getAttribute(Product::ENTITY, $attributeCode)
                            ->getAttributeCode();
                    }
                    if (!empty($attributeValue['from']) || !empty($attributeValue['to'])) {
                        if (!empty($attributeValue['from'])) {
                            $this->requestBuilder->bind("{$attributeCode}.from", $attributeValue['from']);
                        }
                        if (!empty($attributeValue['to'])) {
                            $this->requestBuilder->bind("{$attributeCode}.to", $attributeValue['to']);
                        }
                    } elseif (!is_array($attributeValue)) {
                        $this->requestBuilder->bind($attributeCode, $attributeValue);
                    } else if (isset($attributeValue['like'])) {
                        $this->requestBuilder->bind($attributeCode, trim($attributeValue['like'], '%'));
                    } else {
                        $this->requestBuilder->bind($attributeCode, $attributeValue['in']);
                    }
                }
            }
            $queryRequest = $this->requestBuilder->create();
            $queryResponse = $this->searchEngine->search($queryRequest);

            $ids = [0];
            /** @var \Magento\Framework\Search\Document $document */
            foreach ($queryResponse as $document) {
                $ids[] = $document->getId();
            }

            $this->addIdFilter($ids);
        }
        return parent::_renderFiltersBefore();
    }
}
