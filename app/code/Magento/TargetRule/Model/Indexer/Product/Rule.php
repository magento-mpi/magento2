<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_TargetRule
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TargetRule\Model\Indexer\Product;

class Rule implements \Magento\Indexer\Model\ActionInterface, \Magento\Framework\Mview\ActionInterface
{
    /**
     * @var \Magento\TargetRule\Model\Indexer\Product\Rule\Action\Row
     */
    protected $_productRuleIndexerRow;

    /**
     * @var \Magento\TargetRule\Model\Indexer\Product\Rule\Action\Rows
     */
    protected $_productRuleIndexerRows;

    /**
     * @var \Magento\TargetRule\Model\Indexer\Product\Rule\Action\Full
     */
    protected $_productRuleIndexerFull;

    /**
     * @param Rule\Action\Row $productRuleIndexerRow
     * @param Rule\Action\Rows $productRuleIndexerRows
     * @param Rule\Action\Full $productRuleIndexerFull
     */
    public function __construct(
        \Magento\TargetRule\Model\Indexer\Product\Rule\Action\Row $productRuleIndexerRow,
        \Magento\TargetRule\Model\Indexer\Product\Rule\Action\Rows $productRuleIndexerRows,
        \Magento\TargetRule\Model\Indexer\Product\Rule\Action\Full $productRuleIndexerFull
    ) {
        $this->_productRuleIndexerRow = $productRuleIndexerRow;
        $this->_productRuleIndexerRows = $productRuleIndexerRows;
        $this->_productRuleIndexerFull = $productRuleIndexerFull;
    }

    /**
     * Execute materialization on ids entities
     *
     * @param int[] $ids
     *
     * @return void
     */
    public function execute($ids)
    {
        $this->_productRuleIndexerRows->execute($ids);
    }

    /**
     * Execute full indexation
     *
     * @return void
     */
    public function executeFull()
    {
        $this->_productRuleIndexerFull->execute();
    }

    /**
     * Execute partial indexation by ID list
     *
     * @param int[] $ids
     *
     * @return void
     */
    public function executeList($ids)
    {
        $this->_productRuleIndexerRows->execute($ids);
    }

    /**
     * Execute partial indexation by ID
     *
     * @param int $id
     *
     * @return void
     */
    public function executeRow($id)
    {
        $this->_productRuleIndexerRow->execute($id);
    }
}
