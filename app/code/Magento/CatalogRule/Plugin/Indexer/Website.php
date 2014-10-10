<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogRule\Plugin\Indexer;

use \Magento\CatalogRule\Model\Indexer\Rule\RuleProductProcessor;

class Website
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
     * @param \Magento\Store\Model\Website $website
     * @return \Magento\Store\Model\Website
     */
    public function afterDelete(\Magento\Store\Model\Website $website)
    {
        $this->ruleProductProcessor->markIndexerAsInvalid();
        return $website;
    }
}
