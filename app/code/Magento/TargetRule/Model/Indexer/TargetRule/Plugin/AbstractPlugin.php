<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_TargetRule
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\TargetRule\Model\Indexer\TargetRule\Plugin;

use \Magento\TargetRule\Model\Indexer\TargetRule\Rule\Product\Processor as RuleProductProcessor;
use \Magento\TargetRule\Model\Indexer\TargetRule\Product\Rule\Processor as ProductRuleProcessor;

abstract class AbstractPlugin
{
    /**
     * @var ProductRuleProcessor
     */
    protected $_productRuleindexer;

    /**
     * @var RuleProductProcessor
     */
    protected $_ruleProductIndexer;

    /**
     * @param ProductRuleProcessor $productRuleProcessor
     * @param RuleProductProcessor $ruleProductProcessor
     */
    public function __construct(ProductRuleProcessor $productRuleProcessor, RuleProductProcessor $ruleProductProcessor)
    {
        $this->_productRuleindexer = $productRuleProcessor;
        $this->_ruleProductIndexer = $ruleProductProcessor;
    }

    /**
     * Invalidate indexers
     *
     * @return $this
     */
    protected function invalidateIndexers()
    {
        $this->_productRuleindexer->markIndexerAsInvalid();
        $this->_ruleProductIndexer->markIndexerAsInvalid();
        return $this;
    }
}
