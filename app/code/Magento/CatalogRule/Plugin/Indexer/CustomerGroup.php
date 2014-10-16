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
     * @param \Magento\Customer\Model\Group $group
     * @param \Magento\Customer\Model\Group $result
     * @return \Magento\Customer\Model\Group
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterDelete(
        \Magento\Customer\Model\Group $group,
        \Magento\Customer\Model\Group $result
    ) {
        $this->ruleProductProcessor->markIndexerAsInvalid();
        return $result;
    }
}
