<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogRule\Plugin\Indexer;

use \Magento\CatalogRule\Model\Indexer\Rule\RuleProductProcessor;

class CustomerGroup
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
     * @param bool $result
     * @return bool
     */
    public function afterDeleteGroup($result)
    {
        $this->ruleProductProcessor->markIndexerAsInvalid();
        return $result;
    }
}
