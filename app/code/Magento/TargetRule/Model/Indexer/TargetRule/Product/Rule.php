<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_TargetRule
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TargetRule\Model\Indexer\TargetRule\Product;

use \Magento\Indexer\Model\Indexer\State;

class Rule implements \Magento\Indexer\Model\ActionInterface, \Magento\Framework\Mview\ActionInterface
{
    /**
     * @var \Magento\TargetRule\Model\Indexer\TargetRule\Product\Rule\Action\Row
     */
    protected $_productRuleIndexerRow;

    /**
     * @var \Magento\TargetRule\Model\Indexer\TargetRule\Product\Rule\Action\Rows
     */
    protected $_productRuleIndexerRows;

    /**
     * @var \Magento\TargetRule\Model\Indexer\TargetRule\Action\Full
     */
    protected $_productRuleIndexerFull;

    /**
     * @var \Magento\TargetRule\Model\Indexer\TargetRule\Rule\Product\Processor
     */
    protected $_ruleProductProcessor;

    /**
     * @var Rule\Processor
     */
    protected $_productRuleProcessor;

    /**
     * @param \Magento\TargetRule\Model\Indexer\TargetRule\Product\Rule\Action\Row $productRuleIndexerRow
     * @param \Magento\TargetRule\Model\Indexer\TargetRule\Product\Rule\Action\Rows $productRuleIndexerRows
     * @param \Magento\TargetRule\Model\Indexer\TargetRule\Action\Full $productRuleIndexerFull
     * @param \Magento\TargetRule\Model\Indexer\TargetRule\Rule\Product\Processor $ruleProductProcessor
     * @param \Magento\TargetRule\Model\Indexer\TargetRule\Product\Rule\Processor $productRuleProcessor
     */
    public function __construct(
        \Magento\TargetRule\Model\Indexer\TargetRule\Product\Rule\Action\Row $productRuleIndexerRow,
        \Magento\TargetRule\Model\Indexer\TargetRule\Product\Rule\Action\Rows $productRuleIndexerRows,
        \Magento\TargetRule\Model\Indexer\TargetRule\Action\Full $productRuleIndexerFull,
        \Magento\TargetRule\Model\Indexer\TargetRule\Rule\Product\Processor $ruleProductProcessor,
        \Magento\TargetRule\Model\Indexer\TargetRule\Product\Rule\Processor $productRuleProcessor
    ) {
        $this->_productRuleIndexerRow = $productRuleIndexerRow;
        $this->_productRuleIndexerRows = $productRuleIndexerRows;
        $this->_productRuleIndexerFull = $productRuleIndexerFull;
        $this->_ruleProductProcessor = $ruleProductProcessor;
        $this->_productRuleProcessor = $productRuleProcessor;
    }

    /**
     * Execute materialization on ids entities
     *
     * @param int[] $productIds
     *
     * @return void
     */
    public function execute($productIds)
    {
        $this->_productRuleIndexerRows->execute($productIds);
    }

    /**
     * Execute full indexation
     *
     * @return void
     */
    public function executeFull()
    {
        if (!$this->_ruleProductProcessor->isFullReindexPassed()) {
            $this->_productRuleIndexerFull->execute();
            $this->_productRuleProcessor->setFullReindexPassed();
        }
    }

    /**
     * Execute partial indexation by ID list
     *
     * @param int[] $productIds
     *
     * @return void
     */
    public function executeList($productIds)
    {
        $this->_productRuleIndexerRows->execute($productIds);
    }

    /**
     * Execute partial indexation by ID
     *
     * @param int $productId
     *
     * @return void
     */
    public function executeRow($productId)
    {
        $this->_productRuleIndexerRow->execute($productId);
    }
}
