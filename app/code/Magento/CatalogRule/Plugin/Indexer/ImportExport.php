<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_TargetRule
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogRule\Plugin\Indexer;

use \Magento\CatalogRule\Model\Indexer\Rule\RuleProductProcessor;
use \Magento\ImportExport\Model\Import;

class ImportExport
{
    /**
     * @var RuleProductProcessor
     */
    protected $ruleProductProcessor;

    /**
     * @param RuleProductProcessor $ruleProductProcessor
     */
    public function __construct(RuleProductProcessor $ruleProductProcessor)
    {
        $this->ruleProductProcessor = $ruleProductProcessor;
    }

    /**
     * Invalidate catalog price rule indexer
     *
     * @param Import $subject
     * @param bool $result
     * @return bool
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterImportSource(Import $subject, $result)
    {
        $this->ruleProductProcessor->markIndexerAsInvalid();
        return $result;
    }
}
