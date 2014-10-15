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
     * Date
     *
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;

    /**
     * @var \Magento\Framework\Search\Request\Builder
     */
    private $requestBuilder;

    /**
     * @var \Magento\Framework\Search\AdapterInterface
     */
    private $searchAdapter;

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
     * @param \Magento\Search\Model\AdapterFactory $searchAdapterFactory
     * @param \Zend_Db_Adapter_Abstract $connection
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     * @internal param \Magento\Framework\Search\Adapter\Mysql\Adapter $searchAdapter
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
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\Search\Request\Builder $requestBuilder,
        \Magento\Search\Model\AdapterFactory $searchAdapterFactory,
        $connection = null
    ) {
        $this->_date = $date;
        $this->requestBuilder = $requestBuilder;
        $this->searchAdapter = $searchAdapterFactory->create();
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
            $this->requestBuilder->bindDimension('scope', $this->getStoreId());
            $this->requestBuilder->setRequestName('advanced_search_container');
            foreach ($fields as $attributes) {
                foreach ($attributes as $attributeCode => $attributeValue) {
                    if (is_numeric($attributeCode)) {
                        $attributeCode = $this->_eavConfig->getAttribute(Product::ENTITY, $attributeCode)
                            ->getAttributeCode();
                    }
                    if (!empty($attributeValue['from']) || !empty($attributeValue['to'])) {
                        if (!empty($attributeValue['from'])) {
                            $this->requestBuilder->bind("{$attributeCode}_from", $attributeValue['from']);
                        }
                        if (!empty($attributeValue['to'])) {
                            $this->requestBuilder->bind("{$attributeCode}_to", $attributeValue['to']);
                        }
                    } elseif (!is_array($attributeValue)) {
                        $this->requestBuilder->bind($attributeCode, $attributeValue);
                    } else {
                        $this->requestBuilder->bind($attributeCode, trim($attributeValue['like'], '%'));
                    }
                }
            }
            $queryRequest = $this->requestBuilder->create();

            $queryResponse = $this->searchAdapter->query($queryRequest);
            $ids = [];
            /** @var \Magento\Framework\Search\Document $document */
            foreach ($queryResponse as $document) {
                $ids[] = $document->getId();
            }

            $this->addFieldToFilter('entity_id', array('in' => $ids));
        }

        return $this;
    }
}
