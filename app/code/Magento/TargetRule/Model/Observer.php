<?php
/**
 * {license_notice}
 *
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
     * @var \Magento\TargetRule\Model\Indexer\Product\Rule
     */
    protected $_productRuleIndexer;

    /**
     * @param \Magento\Index\Model\Indexer $indexer
     * @param \Magento\TargetRule\Model\Indexer\Product\Rule $productRuleIndexer
     */
    public function __construct(
        \Magento\Index\Model\Indexer $indexer,
        \Magento\TargetRule\Model\Indexer\Product\Rule $productRuleIndexer
    ) {
        $this->_indexer = $indexer;
        $this->_productRuleIndexer = $productRuleIndexer;
    }

    /**
     * Prepare target rule data
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function prepareTargetRuleSave(\Magento\Framework\Event\Observer $observer)
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
     * Process event on 'save_commit_after' event
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function catalogProductSaveCommitAfter(\Magento\Framework\Event\Observer $observer)
    {
        /** @var $product \Magento\Catalog\Model\Product */
        $product = $observer->getEvent()->getProduct();

        $this->_productRuleIndexer->executeRow($product->getId());
    }

    /**
     * Clear customer segment indexer if customer segment is on|off on backend
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function coreConfigSaveCommitAfter(\Magento\Framework\Event\Observer $observer)
    {
        if ($observer->getDataObject()->getPath() == 'customer/magento_customersegment/is_enabled' &&
            $observer->getDataObject()->isValueChanged()
        ) {
            $this->_indexer->logEvent(
                new \Magento\Framework\Object(array('type_id' => null, 'store' => null)),
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
