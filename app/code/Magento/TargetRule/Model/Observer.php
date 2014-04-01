<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_TargetRule
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\TargetRule\Model;

/**
 * TargetRule observer
 *
 */
class Observer
{
    /**
     * @var \Magento\Index\Model\Indexer
     */
    protected $_indexer;

    /**
     * @param \Magento\Index\Model\Indexer $indexer
     */
    public function __construct(\Magento\Index\Model\Indexer $indexer)
    {
        $this->_indexer = $indexer;
    }

    /**
     * Prepare target rule data
     *
     * @param \Magento\Event\Observer $observer
     * @return void
     */
    public function prepareTargetRuleSave(\Magento\Event\Observer $observer)
    {
        $_vars = array('targetrule_rule_based_positions', 'tgtr_position_behavior');
        $_varPrefix = array('related_', 'upsell_', 'crosssell_');
        if ($product = $observer->getEvent()->getProduct()) {
            foreach ($_vars as $var) {
                foreach ($_varPrefix as $pref) {
                    $v = $pref . $var;
                    if ($product->getData($v . '_default') == 1) {
                        $product->setData($v, null);
                    }
                }
            }
        }
    }

    /**
     * After Catalog Product Save - rebuild product index by rule conditions
     * and refresh cache index
     *
     * @param \Magento\Event\Observer $observer
     * @return $this
     */
    public function catalogProductAfterSave(\Magento\Event\Observer $observer)
    {
        /** @var $product \Magento\Catalog\Model\Product */
        $product = $observer->getEvent()->getProduct();

        $this->_indexer->logEvent(
            new \Magento\Object(
                array(
                    'id' => $product->getId(),
                    'store_id' => $product->getStoreId(),
                    'rule' => $product->getData('rule'),
                    'from_date' => $product->getData('from_date'),
                    'to_date' => $product->getData('to_date')
                )
            ),
            \Magento\TargetRule\Model\Index::ENTITY_PRODUCT,
            \Magento\TargetRule\Model\Index::EVENT_TYPE_REINDEX_PRODUCTS
        );
        return $this;
    }

    /**
     * Process event on 'save_commit_after' event
     *
     * @param \Magento\Event\Observer $observer
     * @return void
     */
    public function catalogProductSaveCommitAfter(\Magento\Event\Observer $observer)
    {
        $this->_indexer->indexEvents(
            \Magento\TargetRule\Model\Index::ENTITY_PRODUCT,
            \Magento\TargetRule\Model\Index::EVENT_TYPE_REINDEX_PRODUCTS
        );
    }

    /**
     * Clear customer segment indexer if customer segment is on|off on backend
     *
     * @param \Magento\Event\Observer $observer
     * @return $this
     */
    public function coreConfigSaveCommitAfter(\Magento\Event\Observer $observer)
    {
        if ($observer->getDataObject()->getPath() == 'customer/magento_customersegment/is_enabled' &&
            $observer->getDataObject()->isValueChanged()
        ) {
            $this->_indexer->logEvent(
                new \Magento\Object(array('type_id' => null, 'store' => null)),
                \Magento\TargetRule\Model\Index::ENTITY_TARGETRULE,
                \Magento\TargetRule\Model\Index::EVENT_TYPE_CLEAN_TARGETRULES
            );
            $this->_indexer->indexEvents(
                \Magento\TargetRule\Model\Index::ENTITY_TARGETRULE,
                \Magento\TargetRule\Model\Index::EVENT_TYPE_CLEAN_TARGETRULES
            );
        }
        return $this;
    }
}
