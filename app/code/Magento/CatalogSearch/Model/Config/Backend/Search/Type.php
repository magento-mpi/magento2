<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Catalog Search change Search Type backend model
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\CatalogSearch\Model\Config\Backend\Search;

use Magento\CatalogSearch\Model\Indexer\Fulltext as FulltextIndexer;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\CatalogSearch\Model\Fulltext;
use Magento\Framework\App\Config\Value;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\Model\Resource\AbstractResource;
use Magento\Framework\Data\Collection\Db;
use Magento\Indexer\Model\IndexerRegistry;

class Type extends Value
{
    /**
     * Catalog search fulltext
     *
     * @var Fulltext
     */
    protected $_catalogSearchFulltext;

    /** @var IndexerRegistry */
    protected $indexerRegistry;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param ScopeConfigInterface $config
     * @param Fulltext $catalogSearchFulltext
     * @param IndexerRegistry $indexerRegistry
     * @param AbstractResource $resource
     * @param Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ScopeConfigInterface $config,
        Fulltext $catalogSearchFulltext,
        IndexerRegistry $indexerRegistry,
        AbstractResource $resource = null,
        Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_catalogSearchFulltext = $catalogSearchFulltext;
        $this->indexerRegistry = $indexerRegistry;
        parent::__construct($context, $registry, $config, $resource, $resourceCollection, $data);
    }

    /**
     * After change Catalog Search Type process
     *
     * @return $this
     */
    protected function _afterSave()
    {
        $newValue = $this->getValue();
        $oldValue = $this->_config->getValue(
            Fulltext::XML_PATH_CATALOG_SEARCH_TYPE,
            $this->getScope(),
            $this->getScopeId()
        );
        if ($newValue != $oldValue) {
            $this->_catalogSearchFulltext->resetSearchResults();
            $this->indexerRegistry->get(FulltextIndexer::INDEXER_ID)->invalidate();
        }
        return $this;
    }
}
