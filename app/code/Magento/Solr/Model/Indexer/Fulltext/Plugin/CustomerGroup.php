<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Solr\Model\Indexer\Fulltext\Plugin;

use Magento\CatalogSearch\Model\Indexer\Fulltext\Plugin\AbstractPlugin;
use Magento\Solr\Helper\Data;

class CustomerGroup extends AbstractPlugin
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * @param \Magento\Indexer\Model\IndexerRegistry $indexerRegistry
     * @param Data $helper
     */
    public function __construct(
        \Magento\Indexer\Model\IndexerRegistry $indexerRegistry,
        Data $helper
    ) {
        parent::__construct($indexerRegistry);
        $this->helper = $helper;
    }


    /**
     * Invalidate indexer on customer group save
     *
     * @param \Magento\Customer\Model\Resource\Group $subject
     * @param \Closure $proceed
     * @param \Magento\Framework\Model\AbstractModel $group
     *
     * @return \Magento\Catalog\Model\Resource\Attribute
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundSave(
        \Magento\Customer\Model\Resource\Group $subject,
        \Closure $proceed,
        \Magento\Framework\Model\AbstractModel $group
    ) {
        $needInvalidation = $this->helper->isThirdPartyEngineAvailable()
            && ($group->isObjectNew() || $group->dataHasChangedFor('tax_class_id'));
        $result = $proceed($group);
        if ($needInvalidation) {
            $this->indexerRegistry->get(Fulltext::INDEXER_ID)->invalidate();
        }
        return $result;
    }
}
